<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReportUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportedUserController extends Controller
{

    public function index(Request $request, User $user)
    {
        if ($request->ajax()) {

            $reportedUsers = DB::table('report_users')->select('report_to')
                ->orderBy('created_at', 'desc')
                ->pluck('report_to')
                ->toArray();

            $reportedUsers = array_unique($reportedUsers, SORT_REGULAR);
            $sorter = static function ($produto) use ($reportedUsers) {
                return array_search($produto->id, $reportedUsers);
            };
            $users = User::whereIn('id', $reportedUsers)->get()->sortBy($sorter);
            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('total_report', function ($user) {
                return ReportUser::where([
                    'report_to' => $user->id
                ])->count();
            })
                ->addColumn('active_status', function ($user) {
                $status = '';
                if ($user->active_status == 1) {

                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle_1" role_title="Customer" user_id="' . $user->id . '" value="' . $user->active_status . '" checked >
                    <span class="slider round" data-toggle="tooltip"  title="Suspend"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Active</span>';
                } else {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle_1" role_title="Customer" user_id="' . $user->id . '" value="' . $user->active_status . '" >
                    <span class="slider round" data-toggle="tooltip"  title="Active"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Suspend</span>';
                }
                return $status;
            })
                ->addColumn('created_at', function ($user) {
                return changeTimeZone($user->created_at);
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($user) {
                $btn = '';
                $btn = '<a href="' . route('reportedUsers.show', base64_encode($user->id)) . '" title="View"><i class="fas fa-eye mr-1"></i></a>';
                // $btn .= '<a href="javascript:void(0);" delete_form="delete_customer_form" data-id="' . base64_encode($user->id) . '" class="delete-datatable-record text-danger delete-users-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'active_status'
            ])
                ->make(true);
        }

        return view('admin.reported-users.index');
    }

    public function show(Request $request, ReportUser $reportPost, $id)
    {
        $user = User::findOrFail(base64_decode($id));
        if ($request->ajax()) {

            $posts = ReportUser::latest()->where([
                'report_to' => base64_decode($id)
            ])->get();
            return datatables()->of($posts)
                ->addIndexColumn()
                ->addColumn('user_id', function ($post) {
                return @$post->reportedBy->full_name;
            })
            ->addColumn('report_type', function ($post) {
                return @$post->getReportType();
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($post) {
                $btn = '';
                $btn = '<a href="' . route('reportedPosts.show', base64_encode($post->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($post->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'post_url'
            ])
                ->make(true);
        }
        
        return view('admin.reported-users.view',compact('user'));
    }
}
