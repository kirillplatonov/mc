</div>

<?php echo ads_manager::get_ads_block('all_pages_down', '<div class="adv">', '</div>') ?>

<div class="copy">
    <?php echo date('Y', time()) ?> &copy; <a href="<?php echo URL ?>"><?php echo $GLOBALS['CONFIG']['system']['system_title'] ?></a> (<a title="Гостей онлайн" href="<?php echo a_url('user/list_guests') ?>"><?php echo GUESTS_ONLINE ?></a>/<a title="Пользователей онлайн" href="<?php echo a_url('user/list_users', 'type=online') ?>"><?php echo USERS_ONLINE ?></a>)
</div>

<?php if ( ! empty($GLOBALS['CONFIG']['system']['footer_codes_index']) || ! empty($GLOBALS['CONFIG']['system']['footer_codes_other_pages'])): ?>
<div class="block">
    <?php if (ROUTE_MODULE == 'index_page') echo $GLOBALS['CONFIG']['system']['footer_codes_index'];
    else echo $GLOBALS['CONFIG']['system']['footer_codes_other_pages'];
    ?>
</div>
<?php endif ?>

<!-- copyright -->

</body>
</html>