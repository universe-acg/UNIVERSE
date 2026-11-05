<?php
namespace App\Service;
/** Resolve supported players without fetching remote URLs or trusting arbitrary embed HTML. */
final class StreamPlayer
{
    public static function resolve(?string $link,string $parent='localhost'): ?array
    {
        $link=trim((string)$link);
        if (preg_match('~^/uploads/videos/[a-zA-Z0-9][a-zA-Z0-9._-]*\.(mp4|webm)$~',$link)) return ['kind'=>'video','url'=>$link];
        if (!filter_var($link,FILTER_VALIDATE_URL) || parse_url($link,PHP_URL_SCHEME)!=='https') return null;
        $host=strtolower((string)parse_url($link,PHP_URL_HOST)); $path=(string)parse_url($link,PHP_URL_PATH); parse_str((string)parse_url($link,PHP_URL_QUERY),$query);
        $id=null;
        if ($host==='youtu.be') $id=trim($path,'/');
        if (in_array($host,['youtube.com','www.youtube.com','youtube-nocookie.com','www.youtube-nocookie.com'],true)) $id=$query['v']??(preg_match('~^/(?:embed|shorts|live)/([^/]+)~',$path,$m)?$m[1]:null);
        if (is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/',$id)) return ['kind'=>'iframe','url'=>'https://www.youtube-nocookie.com/embed/'.$id];
        if (in_array($host,['vimeo.com','www.vimeo.com','player.vimeo.com'],true) && preg_match('~^/(?:video/)?(\d+)$~',$path,$m)) return ['kind'=>'iframe','url'=>'https://player.vimeo.com/video/'.$m[1]];
        if (in_array($host,['twitch.tv','www.twitch.tv'],true) && preg_match('~^/([a-zA-Z0-9_]+)$~',$path,$m)) return ['kind'=>'iframe','url'=>'https://player.twitch.tv/?channel='.rawurlencode($m[1]).'&parent='.rawurlencode($parent).'&muted=true'];
        return ['kind'=>'external','url'=>$link];
    }
}
