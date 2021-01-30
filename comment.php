<?php
  if (isset($c['commenter_id'])) {
    $commenter_key = $datastore->key('user', $c['commenter_id']);
    $commenter = $datastore->lookup($commenter_key);

    echo "<div class='comment'>\n";
    echo '<a class="comment-picture-link" href=\'/user/'.$commenter['id']->pathEndIdentifier()."/'><img src=\"https://storage.googleapis.com/thumbnail-assignment-2/".$commenter['id']->pathEndIdentifier().".jpg\" alt=\"".htmlspecialchars($commenter['name'])."\"></a>\n";
    echo "<div class=\"comment-text\" style=\"float:right;max-width: calc(100% - 77px);border-left:1px dashed;padding-left:4px;padding-right:4px;\">\n";
    if (!is_null($commenter)) {
      echo '<div style="display:inline-block;">Comment by: <a href=\'/user/'.$commenter['id']->pathEndIdentifier()."/'>$commenter[name]</a></div>\n";
    }
    echo "<pre>$c[text]</pre>\n";
    echo "</div>\n";
    echo "<div style=\"clear:both;\"></div>\n";
    echo "</div>\n";
  }
?>