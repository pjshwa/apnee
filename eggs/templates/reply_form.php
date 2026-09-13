<div id="nested_comment_form_for_comment_<?= $commentId ?>" class="nested_comment_form_container js-nested-comment-form-container" style="display: none;">
  <form class="nested_comment_form">
    <input type="hidden" id="article_id" name="article_id" value="<?= $articleId ?>"/>
    <input type="hidden" id="comment_id" name="comment_id" value="<?= $commentId ?>"/>
    <h5>▲ 대댓글 달기</h5>
    <p>이름 <input type="text" class="nested_comment_author" name="comment_author" maxlength="30" required/></p>
    <p>내용 <input type="text" class="nested_comment_message" name="comment" maxlength="1000" required/></p>
    <p><input type="submit" class="btn btn-link" value="등록"/></p>
  </form>
</div>
