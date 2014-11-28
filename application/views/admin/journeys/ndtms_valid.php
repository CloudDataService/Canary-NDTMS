<div class="ndtms_valid">   
    <ol>
    <?php
    for($i = 0; $i < count($validation_errors); $i++)
    {
        echo '<li><span>' . $validation_errors[$i] . '</span></li>';	
    }
    ?>
    </ol>
</div>


