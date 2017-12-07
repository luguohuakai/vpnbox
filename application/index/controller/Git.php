<?php

namespace app\index\controller;

class Git
{
    private $file_name = 'git/error';

    // 我的设备首页
    public function index()
    {
        $data = '{
    "ref":"refs/heads/dev",
    "before":"8e62620e86df595fda5f7566b236545092f226bc",
    "commits":[
        {
            "committer":{
                "name":"DM",
                "email":"769245396@qq.com"
            },
            "web_url":"https://coding.net/u/luguohuakai/p/Demo/git/commit/48783ff3ef223dd8285be9bbeb480cf9a74f2c28",
            "short_message":"--
",
            "sha":"48783ff3ef223dd8285be9bbeb480cf9a74f2c28"
        }
    ],
    "after":"48783ff3ef223dd8285be9bbeb480cf9a74f2c28",
    "event":"push",
    "repository":{
        "owner":{
            "path":"/u/luguohuakai",
            "web_url":"https://coding.net/u/luguohuakai",
            "global_key":"luguohuakai",
            "name":"luguohuakai",
            "avatar":"/static/fruit_avatar/Fruit-2.png"
        },
        "https_url":"https://git.coding.net/luguohuakai/Demo.git",
        "web_url":"https://coding.net/u/luguohuakai/p/Demo",
        "project_id":"965479",
        "ssh_url":"git@git.coding.net:luguohuakai/Demo.git",
        "name":"Demo",
        "description":"Demo 示例项目"
    },
    "user":{
        "path":"/u/luguohuakai",
        "web_url":"https://coding.net/u/luguohuakai",
        "global_key":"luguohuakai",
        "name":"luguohuakai",
        "avatar":"/static/fruit_avatar/Fruit-2.png"
    },
    "token":"2734&^YG^R%%*&G%R&*&G^J39d"
}';
        $token = '2734&^YG^R%%*&G%R&*&G^J39d';
// 要检出或pull代码的地方
        $dir = '/srun3/www/demo/';
        $version = 'version.ini';
//        $data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
        $data = json_decode($data);
        L(json_encode($data),$this->file_name);

// 验证token
        if ($data->token !== $token) {
            L('token error', $this->file_name);
            exit('token error');
        }

// 只处理push
        if ($data->event !== 'push') {
            L('you are not push', $this->file_name);
            exit('you are not push');
        }

// 只处理dev分支
        if (!preg_match('/dev/',$data->ref)) {
            L('you are not dev',$this->file_name);
            exit('you are not dev');
        }

        $output = '';
//if (is_file($dir . $version)) {
//    // 第二次及之后直接pull代码
//    $command = 'cd ' . $dir . ' && git pull';
//    $output .= shell_exec($command);
//} else {
        // 第一次先克隆远程代码 切换到dev分支
        $output .= shell_exec("cd {$dir} && git clone {$data->repository->https_url}");
        $output .= shell_exec("cd {$dir}/Demo && git -c core.quotepath=false -c log.showSignature=false checkout -b dev origin/dev --");
//}

// 生成版本信息
        $git_version_commit_num = shell_exec('cd ' . $dir . ' && ' . "git rev-list HEAD | wc -l | awk '{print $1}'");
        $git_version_hash_min = shell_exec('cd ' . $dir . ' && ' . "git rev-list HEAD --abbrev-commit --max-count=1");
        $git_version_hash_max = shell_exec('cd ' . $dir . ' && ' . "git rev-parse HEAD");

        $arr['author'] = 'DM';
        $arr['update_time'] = date('Y-m-d H:i:s');
        $arr['git_version_commit_num'] = $git_version_commit_num;
        $arr['git_version_hash_min'] = $git_version_hash_min;
        $arr['git_version_hash_max'] = $git_version_hash_max;
        $content = '';

        foreach ($arr as $k => $v) {
            $content .= "{$k}={$v}\r\n";
        }

        L($content,$this->file_name);
//        file_put_contents($dir . $version, $content);

        L(json_encode($output),$this->file_name);

    }

}
