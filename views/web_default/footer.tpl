
</div>
</div>
</div>


<!-- низ страницы -->
</div>




<div class="foot_back">
<div class="foot_right">

<div class="link_bot">
Дизайн от <a href="http://art-prime.ru/">Art-Prime</a>
</div>
<div class="link_bot">
<!-- copyright -->
</div>

<?php if(!empty($GLOBALS['CONFIG']['system']['footer_codes_index']) or !empty($GLOBALS['CONFIG']['system']['footer_codes_other_pages'])): ?>
<div class="razd_bot"> </div>
<div class="link_bot">
<?php
if(ROUTE_MODULE == 'index_page') echo $GLOBALS['CONFIG']['system']['footer_codes_index'];
else echo $GLOBALS['CONFIG']['system']['footer_codes_other_pages'];
?>
</div>
<?php endif; ?>


<div class="stat_user">
© <?php echo $GLOBALS['CONFIG']['system']['system_title'] ?>
</div>
</div>
</div>

<div id="clear"></div>
<p></p>



</body>
</html>