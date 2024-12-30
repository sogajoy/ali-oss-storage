<?php
namespace Sogajoy\AliOss;

use Sogajoy\AliOss\Plugins\PutFile;
use Sogajoy\AliOss\Plugins\PutRemoteFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use OSS\Credentials\StaticCredentialsProvider;
use OSS\OssClient;

class OssServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //发布配置文件
        /*
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('alioss.php'),
            ], 'config');
        }
        */

        Storage::extend('oss', function($app, $config)
        {
            $accessId  = $config['access_id'];
            $accessKey = $config['access_key'];

            $cdnDomain = empty($config['cdnDomain']) ? '' : $config['cdnDomain'];
            $bucket    = $config['bucket'];
            $ssl       = empty($config['ssl']) ? false : $config['ssl']; 
            $isCname   = empty($config['isCName']) ? false : $config['isCName'];
            $debug     = empty($config['debug']) ? false : $config['debug'];
            $region     = empty($config['region']) ? '' : $config['region'];
            $signatureVersion     = (empty($config['signatureVersion']) || OssClient::OSS_SIGNATURE_VERSION_V4!=$config['signatureVersion']) ? OssClient::OSS_SIGNATURE_VERSION_V1 : $config['signatureVersion'];

            $endPoint  = $config['endpoint']; // 默认作为外部节点
            $epInternal= $isCname?$cdnDomain:(empty($config['endpoint_internal']) ? $endPoint : $config['endpoint_internal']); // 内部节点
            
            if($debug) Log::debug('OSS config:', $config);

            /* 升级签名算法V4 start */
            $provider = new StaticCredentialsProvider($accessId, $accessKey);
            $config = array(
                "provider" => $provider,
                "endpoint" => $endPoint,
                "signatureVersion" => $signatureVersion,
                "region" => $region,
                "is_cname" => $isCname,
                "domain" => $cdnDomain,
                "use_ssl" => $ssl,
                "internal" => $epInternal
            );
            $client = new OssClient($config);
            /* 升级签名算法V4 end */
            //$client  = new OssClient($accessId, $accessKey, $epInternal, $isCname);
            $adapter = new OssAdapter($client, $bucket, $endPoint, $ssl, $isCname, $debug, $cdnDomain);

            //Log::debug($client);
            $filesystem =  new Filesystem($adapter);
            
            $filesystem->addPlugin(new PutFile());
            $filesystem->addPlugin(new PutRemoteFile());
            //$filesystem->addPlugin(new CallBack());
            return $filesystem;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

}
