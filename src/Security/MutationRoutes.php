<?php
namespace App\Security;
use Symfony\Component\Routing\RouterInterface;
final class MutationRoutes
{
    public const METHODS=['enabledisable','accept','acceptxml','reject','cancelnotxml','cancelxml','kick','kickxml','leave','leavenonxml','remove','deleteforadmin','deleterepeatingevent','deleterepeatingeventadministrator','deleterepeatingeventorganizer','capturesms','mailservertest','handlepostrequest','addremoveattendee','addremove','resendconfirmationemail','sendmessage','savemessage','deletemessage','deletegbmemberbyid','updategbmembersbyorg','addgbmembersbyorg','delete','delete_administrator','deletecomment','restore','showhide','featured','publish','draft','cancel','cancel_administrator','follow','unfollow','removeuserfromchat','mutechat','addusertochat','makerequest','bookreservation','cancelreservation','deletereservation','applylisting','apply','like','unlike','vote','validate','bookevent','markallnotificationsasread','marknotificationasread','marknotificationasreadxhr','emptycart'];
    private ?array $cachedPaths = null;
    public function __construct(private RouterInterface $router) {}
    public static function isMutation(?string $controller): bool {
        if(!$controller||!str_contains($controller,'::'))return false;
        if(str_contains($controller,'ManagementClubsSocietiesController::add'))return true;
        return in_array(strtolower(explode('::',$controller,2)[1]),self::METHODS,true);
    }
    public function paths(): array {
        if($this->cachedPaths!==null)return $this->cachedPaths;
        $paths=[];foreach($this->router->getRouteCollection() as $route)if(self::isMutation($route->getDefault('_controller')))$paths[]=$route->getPath();
        return $this->cachedPaths=array_values(array_unique($paths));
    }
}
