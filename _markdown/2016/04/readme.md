<!-- date: 20160413 -->
README
========

kecho
----
一个简单的 markdown 静态博客


输入
----
博客保存格式為 `.md`，并放到 `_markdown` 目录下（按年月存放，如 `_markdown/2015/05/halo.md` ）。


说明：
```
<!-- date: ____DATE____ -->
____TITLE____
========

____CONTENT____
```


范例：
```
<!-- date: 20160413 -->
这是我的第一个博客
========

这里是博客内容，记得博客内容与大标题之前留空一行。
```

PS： `博客附件（如图片等静态文件）` 存放的路径我个人用 `_attachment`，其实放到 `kecho` 目录下任何地方都行，只要能被访问到。



输出
----
假设 `.md` 文件位于 `_markdown/2016/04/halo.md`。
输出的 `.html` 文件则位于 `article/2016/04/halo.html`，以及附带输出在根目录的 `index.html`。

PS： `_markdown`, `article` 目录名字均可配置，具体请参考配置章节。


用法
----
终端下运行 `php make.php`


配置
----
请查看 `config.php` 里的注释


其他：
如果需要 `gulp` 编译环境，记得先 `npm install -g gulp` 安裝。