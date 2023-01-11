@extends('layouts.guest')
@section('content')
@section('title') About Us @endsection

<!-- banner-section start-->

<!-- banner-section end-->


<!-- findjob-section end-->
<!-- about-section end -->

 <!-- section app -->
 <section class="Conditions-section pt-5 pb-30 Poppins m-5">
 @if(!empty($page))
  <h2 class="mt-1">{!!$page->title!!}</h2>
    <hr />
    {!!$page->description!!}
 @else
	
<div class="terms-heading text-center py-10">About Us</div>
<div class="Privacy">
<p>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere vulputate. Etiam elit elit, elementum sed varius at, adipiscing vitae est. Sed nec felis pellentesque, lacinia dui sed, ultricies sapien. Pellentesque orci lectus, consectetur vel posuere posuere, rutrum eu ipsum. Aliquam eget odio sed ligula iaculis consequat at eget orci. Mauris molestie sit amet metus mattis varius. Donec sit amet ligula eget nisi sodales egestas. Aliquam interdum dolor aliquet dolor sollicitudin fermentum. Donec congue lorem a molestie bibendum. Etiam nisi ante, consectetur eget placerat a, tempus a neque. Donec ut elit urna. Etiam venenatis eleifend urna eget scelerisque. Aliquam in nunc quis dui sollicitudin ornare ac vitae lectus.</p>
</div>
<div class="terms-heading text-center py-10">Lorem ipsum dolor</div>
<div class="privacy">
<ul>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget .</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero .</li>
<li>Mauris non tempor quam, et lacinia sapien. Mauris accumsan eros eget libero posuere.</li>
</ul>
</div>

@endif
 </section>




@endsection


