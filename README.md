# Aliyun OSS Filesystem Storage for Larevel

# 概述
这是一个基于Laravel文件存储Filesystem Storage开发的支持阿里云OSS的扩展包，支持Laravel 5+。您仅需要加入一些配置即可通过Laravel的Storage无缝使用阿里云OSS作为文件存储引擎。本项目主要代码来源于“jacobcyl/ali-oss-storage”提供，本人仅优化了少部分代码，如更新了支持阿里云OSS接口签名算法V4版本。

## 特别感谢
- [jacobcyl/ali-oss-storage](https://github.com/jacobcyl/Aliyun-oss-storage)

## 运行
- Laravel 5+
- cURL extension

## 安装&配置
1、通过composer安装:
```php
    composer require sogajoy/ali-oss-storage:^2.4
```
2、在 config/app.php 文件中加入如下配置：
```php
Sogajoy\AliOss\OssServiceProvider::class,
```
3、在 app/filesystems.php 文件加入如下配置:
```php
'disks'=>[
    ...
    'oss' => [
            'driver'        => 'oss',
            'access_id'     => '<Your Aliyun OSS AccessKeyId>',
            'access_key'    => '<Your Aliyun OSS AccessKeySecret>',
            'bucket'        => '<OSS bucket name>',
            'endpoint'      => '<the endpoint of OSS>', // OSS 外网节点或自定义外部域名
            //'endpoint_internal' => '<internal endpoint [OSS内网节点] 如：oss-cn-shenzhen-internal.aliyuncs.com>', // v2.0.4 新增配置属性，如果为空，则默认使用 endpoint 配置(由于内网上传有点小问题未解决，请大家暂时不要使用内网节点上传，正在与阿里技术沟通中)
            'cdnDomain'     => '<CDN domain, cdn域名>', // 如果isCName为true, getUrl会判断cdnDomain是否设定来决定返回的url，如果cdnDomain未设置，则使用endpoint来生成url，否则使用cdn
            'ssl'           => <true|false> // true to use 'https://' and false to use 'http://'. default is false,
            'isCName'       => <true|false> // 是否使用自定义域名,true: 则Storage.url()会使用自定义的cdn或域名生成文件url， false: 则使用外部节点生成url
            'debug'         => <true|false>
            'region'        => '<OSS region>',
            'signatureVersion' => '<Signature Version: v1 | v4>',
    ],
    ...
]
```
4、如果您的项目文件存储都为阿里云OSS，可将 app/filesystems.php默认驱动改为oss:
```php
'default' => 'oss',
```
这样您将可以通过Laravel的Storage使用阿里云OSS文件存储了！

## 示例代码   

```php
Storage::disk('oss'); //如果默认驱动不是oss，需指定驱动为oss

//获取指定bucket路径下的文件
Storage::files($directory);
Storage::allFiles($directory);

Storage::put('path/to/file/file.jpg', $contents); //第一个参数是目标文件路径，第二个参数是文件内容
Storage::putFile('path/to/file/file.jpg', 'local/path/to/local_file.jpg'); //从本地路径上传文件到OSS

Storage::get('path/to/file/file.jpg'); //通过路径获取文件对象
Storage::exists('path/to/file/file.jpg'); //确定文件是否存在
Storage::size('path/to/file/file.jpg'); //获取文件大小（字节）
Storage::lastModified('path/to/file/file.jpg'); //获取上次修改日期

Storage::directories($directory); //获取给定目录中的所有目录
Storage::allDirectories($directory); //获取给定目录中的所有（递归）目录

Storage::copy('old/file1.jpg', 'new/file1.jpg');   //复制文件
Storage::move('old/file1.jpg', 'new/file1.jpg');   //移动文件
Storage::rename('path/to/file1.jpg', 'path/to/file2.jpg');     //重命名文件

Storage::prepend('file.log', 'Prepended Text'); //向文件内容前追加内容
Storage::append('file.log', 'Appended Text'); //向文件内容后追加内容

Storage::delete('file.jpg');    //删除文件
Storage::delete(['file1.jpg', 'file2.jpg']);    //删除多个文件

Storage::makeDirectory($directory); //创建目录
Storage::deleteDirectory($directory); // 递归删除目录。它将删除给定目录中的所有文件，因此请谨慎使用。

Storage::putRemoteFile('target/path/to/file/jacob.jpg', 'http://example.com/jacob.jpg'); //将远程url文件上传到OSS

Storage::url('path/to/img.jpg') //获取文件url地址
```
## License

- MIT
