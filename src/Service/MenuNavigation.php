<?php
namespace App\Service;
use App\Entity\MenuElement;
use App\Repository\MenuRepository;
use Symfony\Bundle\SecurityBundle\Security;
/** One source of link normalization and audience rules for header and footer menus. */
final class MenuNavigation
{
    public function __construct(private MenuRepository $menus,private Security $security) {}
    public static function isSafeLink(string $link): bool {
        if(preg_match('/[\x00-\x20\x7f\\\\]/',$link))return false;
        if(str_starts_with($link,'/')&&!str_starts_with($link,'//'))return true;
        if(str_starts_with($link,'#'))return (bool)preg_match('/^#[a-zA-Z][\w-]*$/',$link);
        $scheme=strtolower((string)parse_url($link,PHP_URL_SCHEME));
        if(in_array($scheme,['http','https'],true))return filter_var($link,FILTER_VALIDATE_URL)!==false;
        return in_array($scheme,['mailto','tel'],true)&&strlen(substr($link,strlen($scheme)+1))>0;
    }
    public static function normalizeLink(?string $value): ?string {
        $value=trim((string)$value);if($value===''||!self::isSafeLink($value))return null;
        $host=strtolower((string)parse_url($value,PHP_URL_HOST));
        if($host==='universe.alexandroskourtis.me')$value=(parse_url($value,PHP_URL_PATH)?:'/').(($query=parse_url($value,PHP_URL_QUERY))?'?'.$query:'');
        return ['/open-events'=>'/events','/api/profile/change-email'=>'/dashboard/change-email'][$value]??$value;
    }
    public function isVisible(MenuElement $element): bool {
        $user=$this->security->getUser();
        $visible=match($element->getAudience()) {
            'everyone'=>true,'guests'=>$user===null,'authenticated'=>$user!==null,
            'attendees'=>$this->security->isGranted('ROLE_ATTENDEE'),
            'organizers'=>$this->security->isGranted('ROLE_ORGANIZER'),
            'administrators'=>$this->security->isGranted('ROLE_ADMINISTRATOR'),default=>false,
        };
        if(!$visible)return false;
        $link=self::normalizeLink($element->getLink()?:$element->getCustomLink());
        if(!$link)return in_array($element->getLink(),['categories_dropdown','footer_categories_section'],true);
        $path=parse_url($link,PHP_URL_PATH)?:'';
        if(str_starts_with($path,'/dashboard/administrator'))return $this->security->isGranted('ROLE_ADMINISTRATOR');
        if(str_starts_with($path,'/dashboard/attendee'))return $this->security->isGranted('ROLE_ATTENDEE');
        if(str_starts_with($path,'/dashboard/organizer'))return $this->security->isGranted('ROLE_ORGANIZER');
        if($path==='/scanqr')return $this->security->isGranted('ROLE_ORGANIZER')||$this->security->isGranted('ROLE_ADMINISTRATOR');
        if(str_starts_with($path,'/dashboard')||$path==='/chat')return $user!==null;
        return true;
    }
    public function menu(string $slug): array {
        $menu=$this->menus->findOneBy(['slug'=>$slug]);$items=[];
        foreach($menu?->getMenuElements()??[] as $element) {
            if(!$this->isVisible($element))continue;
            $raw=$element->getLink()?:$element->getCustomLink();
            $items[]=['label'=>$element->getLabel(),'icon'=>$element->getIcon(),'url'=>self::normalizeLink($raw),'kind'=>in_array($raw,['categories_dropdown','footer_categories_section'],true)?$raw:'link','placement'=>$element->getPlacement()];
        }
        return ['name'=>$menu?->getName()??'','heading'=>$menu?->getHeader()?:$menu?->getName()??'','items'=>$items];
    }
}
