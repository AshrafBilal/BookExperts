@extends('layouts.admin') @section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>Profile</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a
							href="{{route('serviceProviders.index')}}">Service Providers</a></li>
						<li class="breadcrumb-item active">Service Provider Details</li>
					</ol>
				</div>
			</div>
		</div>
		<!-- /.container-fluid -->
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">

			<div class="col-md-12">
				<div class="card-header">
					<h3 class="card-title">Profile Details</h3>
				</div>

				<div class="row">

					<div class="col-md-3">

						<!-- Profile Image -->
						<div class="card-body box-profile">
							<div class="text-center custom_box_img">
								<img class="_profile-user-img img-fluid img-circle_"
									src="{{$serviceProvider->getProfilePhoto()}}"
									alt="User profile picture">
							</div>

							<h3 class="profile-username text-center">{{$serviceProvider->first_name}}
								{{$serviceProvider->last_name}}</h3>

							<p class="text-muted text-center">Service Provider</p>



						</div>

					</div>

					<div class="col-md-3">
						<div class="card-body box-profile">
							<div class="text-center custom_box_img">
								<img class="_profile-user-img img-fluid img-circle_"
									src="{{$serviceProvider->getProfileIdentityFile()}}"
									alt="User profile picture">
							</div>



							@if(!empty($serviceProvider->profile_identity_file))
							<div class="text-center custom_switch">
								@if($serviceProvider->profile_identity_file_status == 0 ) <input
									type="submit" user_id="{{$serviceProvider->id}}"
									value="Change to Approve"
									class="btn btn-success text-center change_profile_status"
									attribute="profile_identity_file_status" status="1"
									message="Are you sure want to change status pending to approved?">
								<input type="submit" user_id="{{$serviceProvider->id}}"
									value="Change to Reject"
									class="btn btn-success text-center reject_common"
									attribute="profile_identity_file_status" status="2"
									attribute1="identity_file_comment_title"
									attribute2="identity_file_comment_description" profile_step="3"
									status="2"
									message="Are you sure want to change status pending to rejected?">
								@endif
								<!--  @if($serviceProvider->profile_identity_file_status !=2)
             			  @else
             			     <input type="submit" user_id="{{$serviceProvider->id}}" value="Change to Approve" class="btn btn-success text-center change_profile_status" attribute="profile_identity_file_status" status="1" message="Are you sure want to change status rejected to approved?">                  
             			  @endif -->
							</div>
							<p class="text-muted text-center">
								Profile Identity File <b>({{$serviceProvider->getFileStatus('profile_identity_file_status')}})</b>
							</p>
							@else
							<p class="text-muted text-center">
								Profile Identity File <b>(Not Submit)</b>
							</p>
							@endif


						</div>
					</div>

					<div class="col-md-3">
						<div class="card-body box-profile">
							<div class="text-center custom_box_img">
								@php $ext = pathinfo($serviceProvider->bank_statement,
								PATHINFO_EXTENSION);@endphp 
								@if($ext == 'pdf') <a
									href="{{$serviceProvider->getBankStatementFile()}}" target="_blank">
									<img class="_profile-user-img img-fluid img-circle_ download_pdf"
									src="{{asset('admin/images/pdf_download.png')}}"
									alt="User profile picture">
								</a> @else <img class="_profile-user-img img-fluid img-circle_"
									src="{{$serviceProvider->getBankStatementFile()}}"
									alt="User profile picture"> @endif




							</div>
							@if(!empty($serviceProvider->bank_statement))


							<div class="text-center custom_switch">
								@if($serviceProvider->bank_statement_file_status == 0 ) <input
									type="submit" user_id="{{$serviceProvider->id}}"
									value="Change to Approve"
									class="btn btn-success text-center change_profile_status"
									attribute="bank_statement_file_status" status="1"
									message="Are you sure want to change status pending to approved?">
								<input type="submit" user_id="{{$serviceProvider->id}}"
									value="Change to Reject"
									class="btn btn-success text-center reject_common"
									attribute="bank_statement_file_status" status="2"
									attribute1="bank_statement_comment_title"
									attribute2="bank_statement_comment_description"
									profile_step="4" status="2"
									message="Are you sure want to change status pending to rejected?">
								@endif

								<!--  @if($serviceProvider->bank_statement_file_status !=2)
             			     <input type="submit" user_id="{{$serviceProvider->id}}" value="Change to Approve" class="btn btn-success text-center change_profile_status" attribute="bank_statement_file_status" status="1" message="Are you sure want to change status rejected to approved?">                  
             			                      
             			  @else
             			  @endif -->
							</div>

							<p class="text-muted text-center">
								Bank Statement File <b>({{$serviceProvider->getFileStatus('bank_statement_file_status')}})</b>
							</p>
							@else
							<p class="text-muted text-center">
								Bank Statement File <b>(Not Submit)</b>
							</p>
							@endif



						</div>
					</div>

					<div class="col-md-3">
						<div class="card-body box-profile">
							<!-- <div class="text-center custom_box_img">

								<video width="100%" controls>
									<source
										src="{{$serviceProvider->getProfileIdentityVideoFile()}}"
										type="video/mp4">
								</video>
							</div> -->
							
							<div id="content-container" class="page-bg text-center custom_box_img" data-bind="css:templateName,bgImage:bgImage">
                                <video id="introVideo" width="100%" height="100%"  autoplay="0" controls="controls" data-bind="visible:!isVideoEnded(),event:{ended:videoEnded}">
                                  <source src="{{$serviceProvider->getProfileIdentityVideoFile()}}" type="video/mp4"> </source>
                                </video>
                            </div>

							@if(!empty($serviceProvider->profile_identity_video))

							<div class="text-center custom_switch">
								@if($serviceProvider->profile_identity_video_status == 0 ) <input
									type="submit" user_id="{{$serviceProvider->id}}"
									value=" Approve"
									class="btn btn-success text-center change_profile_status"
									attribute="profile_identity_video_status" status="1"
									message="Are you sure want to change status pending to approved?">
								<input type="submit" user_id="{{$serviceProvider->id}}"
									value="Change to Reject"
									class="btn btn-success text-center reject_common"
									attribute="profile_identity_video_status" status="2"
									attribute1="identity_video_comment_title"
									attribute2="identity_video_comment_description"
									profile_step="3" status="2"
									message="Are you sure want to change status pending to rejected?">
								@endif

								<!-- @if($serviceProvider->profile_identity_video_status !=2)
             			                         
             			  @elseif($serviceProvider->profile_identity_video_status ==2)
             			     <input type="submit" user_id="{{$serviceProvider->id}}" value="Change to Approve" class="btn btn-success text-center change_profile_status" attribute="profile_identity_video_status" status="1" message="Are you sure want to change status rejected to approved?">                  
             			  @endif -->
							</div>
							<p class="text-muted text-center">
								Profile Identity Video <b>({{$serviceProvider->getFileStatus('profile_identity_video_status')}})</b>
							</p>
							@else
							<p class="text-muted text-center">
								Profile Identity Video <b>(Not Submit)</b>
							</p>
							@endif



						</div>
					</div>


					<!-- /.card-body -->



					<!-- /.card -->


					<!-- /.card -->
				</div>
				<!-- /.col -->
				<!-- About Me Box -->

				<!-- /.card-header -->
				<div class="card-body">
					<strong>Email</strong>

					<p class="text-muted">{{$serviceProvider->email}}</p>

					<hr>

					<strong>Phone Number</strong>

					<p class="text-muted">
						{{$serviceProvider->phone_code}}{{$serviceProvider->phone_number}}
					</p>

					<hr>



					<strong>Location</strong>

					<p class="text-muted">{{$serviceProvider->address}}</p>

					<hr>


					<h2>Work Profile</h2>
					@if(!empty($serviceProvider->workProfile)) <strong>Work Profile
						Status</strong>
					<div class=" custom_switch">
						@if(@$serviceProvider->workProfile->status == 0 ) <input
							type="submit" user_id="{{$serviceProvider->id}}"
							onclick="sweetAlert(this)" value="Change to Approve"
							class="btn btn-success text-center change_work_profile_status"
							attribute="status" status="1"
							message="Are you sure want to change status pending to approved?">
						@elseif(@$serviceProvider->workProfile->status == 1 )
						<p class="text-muted ">
							<b>(Approved)</b>
						</p>
						@endif @if(@$serviceProvider->workProfile->status == 0) <input
							type="submit" user_id="{{$serviceProvider->id}}"
							onclick="sweetAlert(this)" value="Change to Reject"
							class="btn btn-success text-center change_work_profile_status"
							attribute="status" status="2"
							message="Are you sure want to change status  pending to Reject?">
						@elseif(@$serviceProvider->workProfile->status ==2)
						<p class="text-muted ">
							<b>(Rejected)</b>
						</p>
						@endif
					</div>
					<strong>Business Name</strong>

					<p class="text-muted">{{@$serviceProvider->workProfile->business_name}}</p>

					<hr>


					<strong>Service Category</strong>

					<p class="text-muted">{{@$serviceProvider->workProfile->serviceCategory->name}}</p>

					<hr>

					<strong>Business Tag line</strong>

					<p class="text-muted">{{@$serviceProvider->workProfile->tagline_for_business}}</p>

					<hr>


					<strong>Work location</strong>

					<p class="text-muted">{!!@$serviceProvider->workProfile->getWorkLocation()!!}</p>

					<hr>


					<strong>About Business </strong>

					<p class="text-muted">{{@$serviceProvider->workProfile->about_business}}</p>

					<hr>

					<strong>Account Type </strong>

					<p class="text-muted">{{(@$serviceProvider->workProfile->account_type
						== 1)?'Individual':'Business'}}</p>

					<hr>

					<strong>Account Created At</strong>

					<p class="text-muted">{{changeTimeZone($serviceProvider->created_at)}}</p>
					@else
					<p class="text-muted">
						<b>(Not Submit)</b>
					</p>

					@endif

				</div>
				<!-- /.card-body -->
			</div>

			<!-- /.col -->
		</div>
		<!-- /.row -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script>
    var vid = document.getElementById("introVideo");
    vid.autoplay = false;
    vid.load();
</script>
@endsection


