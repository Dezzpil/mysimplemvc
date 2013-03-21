
<? if ( ! empty($css)) foreach ($css as $item) { ?>
    <link rel="stylesheet/css" type="text/css" href="<?=$item;?>" />
<? } ?>

<h1><?=$greetings;?></h1>

<h2><?=$foo;?></h2>

<? if ( ! empty($js)) foreach ($js as $item) { ?>
    <script type="text/javascript" src="<?=$item;?>"></script>
<? } ?>