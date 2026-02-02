<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

        </div><!-- end .row -->
    </div>
</div><!-- end #body -->

<!-- 1. 全宽分割线：不设置 margin-left，确保它横跨整个屏幕 -->
<div style="clear: both; width: 100%; border-top: 1px solid rgba(128,128,128,0.15); height: 1px; margin-top: 50px;"></div>

<!-- 2. 底部内容容器：保持 margin-left 以避开侧边栏 -->
<div class="custom-footer-simple" style="margin-left: 180px; padding: 25px 20px; display: block; overflow: hidden;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <!-- 左侧按钮 -->
            <td width="150">
                <div style="background: #C3C3C3; color: #333 !important; padding: 6px 18px; border-radius: 20px; font-weight: bold; display: inline-block; font-size: 12px; white-space: nowrap;">
                    观影Nav.com
                </div>
            </td>
            <!-- 中间版权 -->
            <td style="font-size: 12px; color: #888; padding-left: 20px;">
                Copyright © 2026 观影Nav <a href="https://beian.miit.gov.cn/" target="_blank" style="color: inherit; text-decoration: none;">鲁ICP备2025181353号</a>
            </td>
            <!-- 右侧链接 -->
            <td align="right" style="font-size: 12px; color: #888;">
                <a href="/archives/546/" style="color: inherit; text-decoration: none;">提交收录</a> / 
                <a href="/archives/548/" style="color: inherit; text-decoration: none;">申请友链</a> / 
                <a href="/archives/545/" style="color: inherit; text-decoration: none;">开放合作</a> / 
                <a href="https://www.guannav.com/sitemap.xml" style="color: inherit; text-decoration: none;">站点地图</a> / 
                <a href="/archives/547/" style="color: inherit; text-decoration: none;">关于观影</a>
            </td>
        </tr>
    </table>
</div>

<style>
/* 手机端适配 */
@media (max-width: 992px ) {
    .custom-footer-simple { margin-left: 0 !important; padding: 20px 10px !important; }
    .custom-footer-simple td { display: block; width: 100%; text-align: center; padding: 5px 0; }
    .custom-footer-simple td[align="right"] { text-align: center !important; }
}
</style>

<!-- 原有 JS 逻辑保持不变 -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function( ) {
        const postImages = document.querySelectorAll('.post-content img, .article-content img');
        postImages.forEach(img => {
            if (img.parentElement.tagName !== 'A') {
                const link = document.createElement('a');
                link.href = img.src;
                link.dataset.fancybox = "gallery";
                img.parentNode.insertBefore(link, img);
                link.appendChild(img);
            } else {
                const link = img.parentElement;
                if (!link.dataset.fancybox) link.dataset.fancybox = "gallery";
            }
        });
        Fancybox.bind("[data-fancybox]", {
            Hash: false,
            Thumbs: { autoStart: false },
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: [],
                    right: ["iterateZoom", "slideshow", "fullScreen", "download", "thumbs", "close"],
                },
            },
            Image: { zoom: true, fit: "contain" }
        });
    });
</script>
<script src="<?php $this->options->themeUrl('assets/js/home_animation_controller_final.js'); ?>"></script>

<!-- F12 控制台彩色打印 -->
<script type="text/javascript">
  console.log(
    "\n %c 观影Nav V1.86 导航主题 %c https://www.guannav.com/ %c\n",
    "color: #fadfa3; background: #030307; padding:5px 0; font-size:12px; border:1px solid #030307;",
    "color: #FFFFFF; background: #F1404B; padding:5px 0; font-size:12px; border:1px solid #F1404B;",
    ""
    );
</script>

<script>
(function(){
var el = document.createElement("script");
el.src = "https://lf1-cdn-tos.bytegoofy.com/goofy/ttzz/push.js?aab80428e8ba1579a87ba9f9055d71dbc01aaae888684e395d3d0122e76d933730632485602430134f60bc55ca391050b680e2741bf7233a8f1da9902314a3fa";
el.id = "ttzz";
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(el, s);
})(window)
</script>

<?php $this->footer(); ?>
</body>
</html>
