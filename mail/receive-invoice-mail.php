<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<p>
    Assalamu Alaikum,
</p>
<p>
    Dear Brother <?= $model->donatedBy->fullname; ?>,
</p>

<p>
    We do confirm your following contribution for BIMT Charity Foundation:
</p>
<p>
    Amount: <?= $model->amount; ?> <?= $model->currency->code; ?><br/>
    Received Date: <?= date('d.m.Y', strtotime($model->created_at)); ?><br/>
    For Month(s): <?= $model->instalment_month; ?> <?= $model->instalment_year; ?><br/>
    Comments: <?= $model->comments; ?>
</p>

<p>
    Your SADAKAH has been received with thanks. 
</p>
<p>

    “কে আছে যে আল্লাহকে উত্তম ঋণ দিবে ? তাহলে  তিনি তা বহুগুনে তার জন্য বৃদ্ধি করবেন এবং তার জন্য উত্তম পুরস্কার রয়েছে।“  [সূরা হাদীদ ৫৭:১১]

</p>

<p>
    Ma'ssalam<br/>

    Finance Control Board<br/>

    BIMT Charity Foundation<br/>
</p>
