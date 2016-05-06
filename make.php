<?php
    include 'config.php';
    include 'vendor/autoload.php';

    use \HyperDown\Parser;

    /**
    * 处理 markdown，输出 html
    *
    */
    class markdownToHtml
    {

        public $config;

        public function __construct($config) {
            $this->config = $config;
        }


        // 拆分路径
        public function preg_path($path) {
            // 正则路径
            // $pattern = '/(.*?)(\/.*\/)(.*)(\.md)/i';
            $pattern = '/(.*?)\/(\d{4})([\\\\|\/].*[\\\\|\/])(.*)(\.md)/i';
            preg_match_all($pattern, $path, $matchs);

            $path_info['dir_root'] = $matchs[1][0];
            $path_info['dir_y'] = $matchs[2][0];
            $path_info['dir_m'] = $matchs[3][0];
            $path_info['dir_ym'] = '/' . $path_info['dir_y'] . $path_info['dir_m'];
            $path_info['file_name'] = $matchs[4][0];
            $path_info['file_ext'] = $matchs[5][0];

            return $path_info;
        }



        // 正则 markdown 里的格式，取出相应内容
        public function convert_content($path) {
            // 打开文件
            $raw_content = file_get_contents($path);

            // 匹配内容
            preg_match('/<!-- date:\s?(\d*)\s?-->\s?\n(.*?)\s?\n========\n?\n?(.*)/is', $raw_content, $match);

            $raw_article['date'] = $match[1];
            $raw_article['title'] = $match[2];
            $raw_article['content'] = $match[3];

            return $raw_article;
        }


        // 强制清空目录
        public function rmdir_recursive($dir) {
            if (!is_dir($dir)) {
                return false;
            }

            $it = new RecursiveDirectoryIterator($dir);
            $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

            foreach($it as $file) {
                if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
                if ($file->isDir()) rmdir($file->getPathname());
                else unlink($file->getPathname());
            }

            rmdir($dir);
        }

        // 排序
        function build_sort($arrays, $sort_key, $sort_order = SORT_DESC, $sort_type = SORT_NUMERIC){
                if(is_array($arrays)){
                    foreach ($arrays as $array){
                        if(is_array($array)){
                            $key_arrays[] = $array[$sort_key];
                        }else{
                            return false;
                        }
                    }
                }else{
                    return false;
                }
                array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
                return $arrays;
            }


        // 生成 article
        public function build_article($articles) {
            // 获取 article 模板路径及内容
            $article_template = file_get_contents('themes/' . $this->config['theme'] . '/article.html');

            // 遍历生成 article 文章 html
            foreach ($articles as $key => $article) {

                // 替换 github 或 七牛 附件地址
                if ($this->config['github_info'] != '') {
                    $article_attachment_origin_path = "../../../" . $this->config['attachment_dir'];
                    $article_attachment_github_path = "https://raw.githubusercontent.com/" . $this->config['github_info'] . "/gh-pages/_attachment";
                    $article_markdown_content = str_replace($article_attachment_origin_path, $article_attachment_github_path, $article['content']['content']);
                }elseif ($this->config['qiniu_info'] != '') {
                    $article_attachment_origin_path = "../../../" . $this->config['attachment_dir'];
                    $article_attachment_qiniu_path = $this->config['qiniu_info'];
                    $article_markdown_content = str_replace($article_attachment_origin_path, $article_attachment_qiniu_path, $article['content']['content']);
                }else{
                    $article_markdown_content = $article['content']['content'];
                }

                //初始化markdown解析类
                $parser = new Parser();
                
                // 虽然命名奇怪，但对应关系会比较好懂
                $____TITLE____ = $article['content']['title'];
                $____DATE____ = $article['content']['date'];
                $____CONTENT____ = $parser->makeHtml($article_markdown_content);

                // 准备「替换」与「被替换」
                $replace_tag = array('____TITLE____', '____DATE____', '____CONTENT____');
                $replace_string = array($____TITLE____, $____DATE____, $____CONTENT____);

                // 替换并返回
                $article_html = str_replace($replace_tag, $replace_string, $article_template);
                $save_dir = $this->config['output_dir'] . $article['path']['dir_ym'];
                $save_path = $this->config['output_dir'] . $article['path']['dir_ym'] . $article['path']['file_name'] .'.html';


                // 创建文件夹
                if (!is_dir($save_dir)) {
                    mkdir($save_dir, 0777, true);
                }

                // 推送文章
                file_put_contents($save_path, $article_html);
                echo "「文章」输出完成： $save_path \n";
            }
        }



        // 生成 article
        public function build_index($articles) {
            // 获取 article 模板路径及内容
            $list_template = file_get_contents('themes/' . $this->config['theme'] . '/list.html');
            $index_template = file_get_contents('themes/' . $this->config['theme'] . '/index.html');;

            // 预留给年份压入
            $____LIST____ = '';
            $year_array = array();


            // 遍历生成 article 文章 html
            foreach ($articles as $key => $article) {


                // 虽然命名奇怪，但对应关系会比较好懂
                $____TITLE____ = $article['content']['title'];
                $____DATE____ = $article['content']['date'];
                $____URL____ = $this->config['output_dir'] . $article['path']['dir_ym'] . $article['path']['file_name'] . '.html';
                $____YEAR____ = '';

                // 年
                $year = $article['path']['dir_y'];

                // 如果年不在上面的年份 array 里，就压入。
                if(!in_array($year, $year_array)) {
                    $____YEAR____ = '<h3 class="year">' . $year . '</h3>';
                    array_push($year_array, $year);
                }


                // 准备「替换」与「被替换」
                $replace_tag = array('____TITLE____', '____DATE____', '____URL____', '____YEAR____');
                $replace_string = array($____TITLE____, $____DATE____, $____URL____, $____YEAR____);

                // 替换并返回
                $____LIST____ .= str_replace($replace_tag, $replace_string, $list_template);
            }

            $index_html = str_replace('____LIST____', $____LIST____, $index_template);

            file_put_contents('index.html', $index_html);
            echo "「首页」输出完成： index.html \n";
        }
    }






    // 来，跑起来！！！！！！！！！
    $markdownToHtml = new markdownToHtml($config);

    // 开始收集所有 markdown 文章
    $articles = array();
    $articles_sort = array();

    // 批处理
    foreach (glob($config['input_dir'] . '/*', GLOB_MARK) as $dir_year) {
        if(is_dir($dir_year)){
            foreach (glob($dir_year.'*', GLOB_MARK) as $dir_month) {
                if(is_dir($dir_month)){
                    foreach (glob($dir_month.'*.md') as $path) {

                        // 拆分路径
                        $path_info = $markdownToHtml->preg_path($path);

                        // 正则内容 & 转换为 html
                        $content_info = $markdownToHtml->convert_content($path);
                        $article['path'] = $path_info;
                        $article['content'] = $content_info;
                        $article['date'] = $article['content']['date'];

                        // 所有 markdown 文章都装 $articles
                        array_push($articles, $article);
                        array_push($articles_sort, $article['date']);
                    }
                }
            }
        }
    }






    // array_multisort($articles_sort, SORT_DESC);
    // print_r($articles);

    // 根据配置判断正序、倒序
    if ($config['sort'] == 'desc') {
        $articles = $markdownToHtml->build_sort($articles, 'date');
    }

    // 清除掉之前生成的目录
    $markdownToHtml->rmdir_recursive($config['output_dir']);

    // 输出文章，完工！
    $markdownToHtml->build_index($articles);
    $markdownToHtml->build_article($articles);

?>