<?php
  if (isset($c['commenter_id'])) {
    $commenter_key = $datastore->key('user', $c['commenter_id']);
    $commenter = $datastore->lookup($commenter_key);

    echo "<div class='comment'>\n";
    echo '<a style="float:left;margin:0;margin-right:4px;padding:1px;" href=\'/user/'.$commenter['id']->pathEndIdentifier()."/'><img src=\"https://storage.googleapis.com/thumbnail-assignment-2/".$commenter['id']->pathEndIdentifier().".jpg\" alt=\"$commenter[name]\" style=\"width:60px;height:60px;display:block;\"></a>\n";
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