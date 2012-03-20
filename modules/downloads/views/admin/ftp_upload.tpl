<?php $this->display('header', array('title' => 'Загрузка файлов с ФТП')) ?>

<style>
#mask {
    position:absolute;
    left:0;
    top:0;
    z-index:9000;
    background-color:#000;
    display:none;
    }
#boxes .window {
    position:absolute;
    left:0;
    top:0;
    width:440px;
    height:200px;
    display:none;
    z-index:9999;
    padding:20px;
    }
#boxes #dialog {
    width:375px;
    height:203px;
    padding:10px;
    background-color:#ffffff;
    }
</style>

<?php if($error): ?>
<div class="error">
<?php echo $error ?>
</div>
<?php endif; ?>

<form action="<?php echo a_url('downloads/admin/ftp_upload', 'directory_id='. @$_GET['directory_id']) ?>" method="post">
<div class="box">
	<h3>Загрузка файлов с ФТП</h3>
	<div class="inside">
	<p>
		<label>Слить в папку</label>
		<b><?php echo $directory['name'] ?></b>
	</p>
	<p>
		<label>Из какой папки сливать</label>
		<input name="from_directory" id="from_directory" type="text" value="" style="width: 400px;"><button id="button_dialog">Обзор</button>
	</p>
    <p>
		<input name="translite" type="checkbox" value="ON" checked="checked"> Транслитерация имён файлов<br />
	</p>
	</div>
</div>

<p><input type="submit" name="submit" value="Загрузить"></p>

</form>

<!-- Окно для выбора папки -->
<div id="boxes">
<div id="dialog" class="window">
	<span style="text-align: right;"><a href="#" class="close">x</a></span>
	<div id="list_directories" style="border: 1px double black; padding: 5px 5px 5px 5px;">
	</div>
	<br />
	Текущая папка:<br />
	<input id="directory"><br />
	<button class="close" onclick="change_directory()";>Выбрать</button>
</div>
</div>

<!-- Макска, которая затемняет весь экран -->
<div id="mask"></div>

<script>
$(document).ready(function() {
    $('#button_dialog').click(function(e) {
    e.preventDefault();
    var id = '#dialog';

    var maskHeight = $(document).height();
    var maskWidth = $(window).width();

    $('#mask').css({'width':maskWidth,'height':maskHeight});

    $('#mask').fadeIn(100);
    $('#mask').fadeTo("slow",0.8);

    var winH = $(window).height();
    var winW = $(window).width();

   	$(id).css('top',  winH/3-$(id).height()/2);
    $(id).css('left', winW/2-$(id).width()/2);

    $(id).fadeIn(200);

    list_directories('');
});

$('.window .close').click(function (e) {
	e.preventDefault();
    $('#mask, .window').hide();
    });

    $('#mask').click(function () {
    $(this).hide();
    $('.window').hide();
    });
});

function list_directories(directory) {	return $.ajax({
	  	type: "GET",
	  	url: "<?php echo a_url('downloads/admin/ftp_upload_get_directories') ?>?directory=" + directory,

	    async: false,
	  	beforeSend: function(){

	   		},
	  	success: function(response){	  		$('#list_directories').empty();
	  		$('#directory').empty();
	  		var directories = eval("(" + response + ")");
	  		var i = 0;
	  		for(var key in directories) {
	    		$('#list_directories').append('<a href="#" onclick="list_directories(\'' + key + '\')">' + directories[key] + '</a><br />');
			    i++;
			}
			if(i == 0) $('#list_directories').append('Папка пуста!');
			$('#directory').val(directory);
		},
	    error: function() {	    	alert('error');
	    }
	}).responseText;
}

function change_directory() {
	var directory = $('#directory').val();    $('#from_directory').val(directory);
}
</script>

<?php $this->display('footer') ?>