<?php
if (!empty($newuserfoot)) {
    include($newuserfoot);
    // 自定义页脚分支原先直接 return，导致响应缺少 </body></html> 闭合标签
    echo "</div>\n</body>\n</html>\n";
    return;
}
?>
</div>
</body>
</html>
