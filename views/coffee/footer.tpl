    <?php echo ads_manager::get_ads_block('all_pages_down', '<div class="rekl">', '</div>') ?>

	</div>

	<?php if ( ! empty($GLOBALS['CONFIG']['system']['footer_codes_index']) || ! empty($GLOBALS['CONFIG']['system']['footer_codes_other_pages'])): ?>
    <?php if (ROUTE_MODULE == 'index_page') echo $GLOBALS['CONFIG']['system']['footer_codes_index'];
    else echo $GLOBALS['CONFIG']['system']['footer_codes_other_pages'];
    ?>
    </div>
    <?php endif ?>


    <div class="foot">
    <?php echo date('Y', time()) ?> &copy; <a href="<?php echo URL ?>"><?php echo $GLOBALS['CONFIG']['system']['system_title'] ?></a> (<a title="Гостей онлайн" href="<?php echo a_url('user/list_guests') ?>"><?php echo GUESTS_ONLINE ?></a>/<a title="Пользователей онлайн" href="<?php echo a_url('user/list_users', 'type=online') ?>"><?php echo USERS_ONLINE ?></a>) :: Design by <a href="http://7art.org.ua">7art</a>
    </div>
    
    <?php if (modules::is_active_module('web_version')): ?>
        <div align="center">
            Версия: <?php echo (WEB_VERSION == '1' ? '<a href="'. URL .'?version=wap">Wap</a> | <u>Web</u>' : '<u>Wap</u> | <a href="'. URL .'?version=web">Web</a>') ?>
        </div> 
    <?php endif ?>

    <!-- copyright -->

</body>
</html>