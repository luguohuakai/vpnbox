<?php

namespace app\index\controller;

class Git
{
    private $file_name = 'git/error';

    // 首页
    public function index()
    {
        $token = '2734&^YG^R%%*&G%R&*&G^J39d';
// 要检出或pull代码的地方
        $dir = '/srun3/www/hook/repos/';
        $version = 'version.ini';
        $data = json_decode(file_get_contents('php://input'));
        L(json_encode($data), $this->file_name);

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
        if (!preg_match('/dev/', $data->ref)) {
            L('you are not dev', $this->file_name);
            exit('you are not dev');
        }

        $output = '';
//if (is_file($dir . $version)) {
        // 第二次及之后直接pull代码
        $command = "cd {$dir}{$data->repository->name} && /usr/local/bin/git pull";
        $output .= shell_exec($command);
        L($command,$this->file_name);
//} else {
        // 有确认 应该也可以解决
//        // 第一次先克隆远程代码 切换到dev分支
//        $output .= shell_exec("cd {$dir} && git clone {$data->repository->ssh_url} 2>&1");
//        L("cd {$dir} && git clone {$data->repository->ssh_url} 2>&1",$this->file_name);
//        $output .= shell_exec("cd {$dir}{$data->repository->name} && git -c core.quotepath=false -c log.showSignature=false checkout -b dev origin/dev 2>&1");
//        L("cd {$dir}{$data->repository->name} && git -c core.quotepath=false -c log.showSignature=false checkout -b dev origin/dev 2>&1",$this->file_name);
//}

// 生成版本信息
        $git_version_commit_num = shell_exec("cd {$dir}{$data->repository->name} && /usr/local/bin/git rev-list HEAD | wc -l | awk '{print $1}'");
        $git_version_hash_min = shell_exec("cd {$dir}{$data->repository->name} && /usr/local/bin/git rev-list HEAD --abbrev-commit --max-count=1");
        $git_version_hash_max = shell_exec("cd {$dir}{$data->repository->name} && /usr/local/bin/git rev-parse HEAD");

        $arr['author'] = 'DM';
        $arr['update_time'] = date('Y-m-d H:i:s');
        $arr['git_version_commit_num'] = trim($git_version_commit_num);
        $arr['git_version_hash_min'] = trim($git_version_hash_min);
        $arr['git_version_hash_max'] = trim($git_version_hash_max);
        $content = '';

        foreach ($arr as $k => $v) {
            $content .= "{$k}={$v}\r\n";
        }

        L($content, $this->file_name);
        file_put_contents($dir . $version, $content);

        L(json_encode($output), $this->file_name);

    }

}
