<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<p>
    Assalamualaikum,<br/>
    Dear Brother <?=$model->fullname;?>,
</p>
<p>
    We hope by the mercy of almighty Allah (SW) you are doing well as well as your family members.<br/> 
    Your member account has been created. Your account will be activate soon inshaAllah after reviewed by our Admin. 
</p>
<p>
    Proposed amount: <?= $model->recurring_amount; ?> <?= $model->currency->code; ?> (more or less amount is unquestionably accepted)<br/>
</p>
<p>
    Insha Allah we will try our level best to use your SADAKAH in right way. Verily, Allah is all knowing and all seeing.
</p>
<p>
    User ID:<?= $model->email; ?><br/>
    Password:<?= $password; ?>
</p>
<p>
    Ma'assalam<br/>
    BIMT Charity Foundation<br/>
</p>