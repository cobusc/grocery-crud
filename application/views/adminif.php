<!DOCTYPE html>
<html>
    <head>
	<meta charset="utf-8" />
        <?php foreach($css_files as $file): ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
        <?php endforeach; ?>
        <?php foreach($js_files as $file): ?>
	    <script src="<?php echo $file; ?>"></script>
        <?php endforeach; ?>
        <style type='text/css'>
body
{
        font-family: Arial;
        font-size: 14px;
}
a {
    color: blue;
    text-decoration: none;
    font-size: 14px;
}
a:hover
{
        text-decoration: underline;
}
        </style>
        <script type="text/javascript">
            function goTo() {
                var sE = null, url;
                if(document.getElementById) {
                    sE = document.getElementById('tableUrl');
                } else if(document.all) {
                    sE = document.all['urlList'];
                }
                if(sE && (url = sE.options[sE.selectedIndex].value)) {
                    location.href = url;
                }
            }
        </script>
    </head>
    <body>
        <div>
            <a href='<?php echo site_url('adminif/index')?>'>INDEX</a>
            <br/><br/>

            <?php if (!empty($tables)): ?>
                <select id="tableUrl" data-placeholder="Choose a table..." class="chosen-select" style="width:350px;" tabindex="2" onchange="goTo();">
                    <option value=""></option> 

                <?php foreach ($tables as $table): ?>
                    <option value="<?php echo site_url("adminif/$table")?>"><?php echo $table?></option>
                <?php endforeach; ?>
                </select>
<!--                 <div class="chzn-container chzn-container-single">
                    <input class="chzn-drop" type="button" value="Go" onclick="goTo();">
                </div> -->
            <?php endif; ?>

            <br/><br/>
            <?php if (!empty($tables))
                  foreach ($tables as $table): ?>
                | <a href='<?php echo site_url("adminif/$table")?>'><?php echo $table?></a><br/>
            <?php endforeach; ?>


	</div>
	
        <div style='height:20px;'>
        </div>  

        <div>
            <?php echo $output; ?>
        </div>
    </body>
</html>
