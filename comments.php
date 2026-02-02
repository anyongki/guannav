<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php function threadedComments($comments, $options) {
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';
        } else {
            $commentClass .= ' comment-by-user';
        }
    }
    $commentLevelClass = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
?>
<li id="li-<?php $comments->theId(); ?>" class="comment-body<?php 
if ($comments->levels > 0) {
    echo ' comment-child';
    $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
} else {
    echo ' comment-parent';
}
$comments->alt(' comment-odd', ' comment-even');
echo $commentClass;
?>">
    <div id="<?php $comments->theId(); ?>" class="comment-inner">
        <div class="comment-author">
            <?php 
            $authorId = $comments->authorId;
            $ownerId = $comments->ownerId;
            if ($authorId && $authorId == $ownerId) {
                // 管理员头像
                echo '<img src="/tx.jpg" class="avatar" width="48" height="48" alt="Admin">';
            } else {
                // 游客随机头像 (使用多套随机源确保多样性)
                $mailHash = md5(strtolower(trim($comments->mail)));
                $randomId = hexdec(substr($mailHash, 0, 2)) % 10 + 1; // 基于邮箱哈希生成 1-10 的随机数
                // 这里可以使用本地图片或者公共随机头像 API，为了稳定，我们生成一个基于哈希的随机 URL
                // 方案：使用 https://api.dicebear.com/7.x/adventurer/svg?seed=HASH
                $randomAvatar = "https://api.dicebear.com/7.x/adventurer/svg?seed=" . $mailHash;
                echo '<img src="' . $randomAvatar . '" class="avatar" width="48" height="48" alt="Guest">';
            }
            ?>
            <span class="fn"><?php $comments->author(false); ?></span>
            <?php if ($authorId && $authorId == $ownerId): ?>
                <span class="comment-badge admin-badge">站长</span>
            <?php endif; ?>
        </div>
        <div class="comment-meta">
            <a href="<?php $comments->permalink(); ?>"><?php $comments->date('Y-m-d H:i'); ?></a>
            <span class="comment-reply"><?php $comments->reply(); ?></span>
        </div>
        <div class="comment-content">
            <?php if ('waiting' == $comments->status) { ?>
                <em class="comment-awaiting-moderation">您的评论正等待审核...</em>
            <?php } ?>
            <?php $comments->content(); ?>
        </div>
    </div>
<?php if ($comments->children) { ?>
    <div class="comment-children">
        <?php $comments->threadedComments($options); ?>
    </div>
<?php } ?>
</li>
<?php } ?>

<div id="comments" class="comments-area">
    <?php $this->comments()->to($comments); ?>
    
    <div class="comments-header">
        <h3 class="section-title"><?php $this->commentsNum(_t('暂无评论'), _t('1 条评论'), _t('%d 条评论')); ?></h3>
    </div>

    <?php if ($comments->have()): ?>
    <div class="comment-list-wrapper">
        <ol class="comment-list">
            <?php $comments->listComments(); ?>
        </ol>
        <div class="comment-pagenav">
            <?php $comments->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="respond">
        <div class="cancel-comment-reply">
            <?php $comments->cancelReply('取消回复'); ?>
        </div>
    
    	<h3 id="response" class="section-title"><?php _t('发表评论'); ?></h3>
    	<form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form" class="comment-form">
            <?php if($this->user->hasLogin()): ?>
    		<p class="comment-user-status">
                <?php _t('登录身份: '); ?>
                <a href="<?php $this->options->profileUrl(); ?>" class="comment-user-name"><?php $this->user->screenName(); ?></a>
                <span class="comment-badge">作者</span>
                <a href="<?php $this->options->logoutUrl(); ?>" title="Logout" class="comment-logout-link"><?php _t('退出'); ?> &raquo;</a>
            </p>
            <?php else: ?>
    		<div class="comment-inputs">
                <input type="text" name="author" id="author" class="text" placeholder="称呼*" value="<?php $this->remember('author'); ?>" required />
                <input type="email" name="mail" id="mail" class="text" placeholder="邮箱*" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> />
                <input type="url" name="url" id="url" class="text" placeholder="网站" value="<?php $this->remember('url'); ?>" />
            </div>
            <?php endif; ?>
    		<div class="comment-textarea-wrapper">
                <textarea rows="5" name="text" id="textarea" class="textarea" placeholder="说点什么吧..." required><?php $this->remember('text'); ?></textarea>
            </div>
    		<div class="comment-form-footer">
                <button type="submit" id="submit-comment" class="submit-comment-btn">提交评论</button>
            </div>
            <?php $this->security->fields(); ?>
    	</form>
    </div>
    <?php else: ?>
    <p class="comments-closed"><?php _t('评论功能已关闭'); ?></p>
    <?php endif; ?>
</div>

<style>
    .comments-area { margin-top: 20px; }
    .comment-list { list-style: none; padding: 0; margin: 0; }
    .comment-list li { padding: 15px 0; list-style: none; }
    .comment-inner { display: flex; flex-direction: column; gap: 8px; padding: 15px; background: var(--bg-color); border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 10px; }
    .comment-author { display: flex; align-items: center; gap: 10px; font-weight: 700; color: var(--text-color); font-size: 15px; font-style: normal; }
    .comment-author .fn { color: var(--text-color); font-style: normal; }
    .comment-author .avatar { width: 32px; height: 32px; border-radius: 50%; }
    .comment-meta { font-size: 12px; color: var(--text-muted); display: flex; justify-content: space-between; align-items: center; }
    .comment-meta a { color: var(--text-muted); text-decoration: none; }
    .comment-content { font-size: 14px; line-height: 1.6; color: var(--text-color); margin-top: 5px; }
    .comment-children { margin-left: 30px; border-left: 2px solid var(--border-color); padding-left: 15px; }
    .comment-awaiting-moderation { font-size: 12px; color: #e67e22; font-style: italic; margin-bottom: 5px; display: block; }
    
    .comment-user-status { margin-bottom: 15px; font-size: 14px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; }
    .comment-user-name { color: var(--accent-color); font-weight: 700; text-decoration: none; }
    .comment-badge { background: var(--accent-color); color: #fff; font-size: 10px; padding: 1px 6px; border-radius: 4px; font-weight: 600; }
    .admin-badge { background: #e74c3c !important; margin-left: 5px; }
    .cancel-comment-reply a { 
        display: inline-block; padding: 4px 12px; background: var(--bg-color); border: 1px solid var(--border-color); 
        border-radius: 6px; color: var(--text-muted); text-decoration: none; font-size: 12px; transition: all 0.3s; 
    }
    .cancel-comment-reply a:hover { background: var(--border-color); color: var(--text-color); }
    .comment-logout-link { color: #e74c3c; text-decoration: none; font-size: 12px; }

    .comment-form { margin-top: 20px; }
    .comment-inputs { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 15px; }
    .comment-inputs input, .comment-textarea-wrapper textarea { 
        width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 10px; 
        background: var(--bg-color); color: var(--text-color); font-size: 14px; transition: all 0.3s; 
    }
    .comment-inputs input:focus, .comment-textarea-wrapper textarea:focus { border-color: var(--accent-color); outline: none; box-shadow: 0 0 0 3px var(--glow-color); }
    .comment-textarea-wrapper { margin-bottom: 15px; }
    
    .comment-form-footer { display: flex; justify-content: flex-end; }
    .submit-comment-btn { 
        background: var(--accent-color); color: #fff; border: none; padding: 12px 30px; border-radius: 10px; 
        cursor: pointer; font-weight: 700; font-size: 15px; transition: all 0.3s;
    }
    .submit-comment-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px var(--glow-color); opacity: 0.9; }
    .submit-comment-btn:active { transform: translateY(0); }
    
    .comments-closed { text-align: center; color: var(--text-muted); padding: 20px; font-style: italic; }
</style>


