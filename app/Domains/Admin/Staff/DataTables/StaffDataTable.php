<?php

namespace App\Domains\Admin\Staff\DataTables;

use App\Domains\Core\User\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Support\Str;

class StaffDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('created_at', function($record) {
                return $record->created_at->format(config('constant.date_format.date'));
            })

            ->editColumn('name', function($record){
                $html = ($record->name ? ucwords($record->name) : '').'<span class="small_email">'.($record->email ?? '').'</span>';
                return $html;
            })

            /* ->editColumn('roles.name', function($record){
                return $record->roles ? $record->roles()->first()->name : '';
            }) */

            ->editColumn('role_name', function($record){
                return $record->role_name ?? '';
            })

            ->editColumn('status', function($record){
                $checkedStatus = '';
                if($record->status == 'active'){
                    $checkedStatus = 'checked';
                }
                return '<div class="checkbox switch">
                    <label>
                        <input type="checkbox" class="switch-control staff_status_cb" '.$checkedStatus.' data-staff_id="'.($record->uuid).'" />
                        <span class="switch-label" data-active="'.trans('global.active').'" data-inactive="'.trans('global.inactive').'"></span>
                    </label>
                </div>';
            })

            ->addColumn('action', function($record){
                $buttons = '<div class="action-col">';

                if (Gate::check('staff_change_password')) {
                    // change password
                    $buttons .= view('Components::action-button', [
                        'route' => route('system-users.change-password', $record->uuid),
                        'class' => 'btnChangePassword',
                        'btnType' => 'change_password',
                    ])->render();
                }

                if (Gate::check('staff_show')) {
                    // view
                    $buttons .= view('Components::action-button', [
                        'route' => route('system-users.show', $record->uuid),
                        'class' => 'btnViewStaff',
                        'btnType' => 'view',
                    ])->render();
                }

                if (Gate::check('staff_edit')) {
                    // edit
                    $buttons .= view('Components::action-button', [
                        'route' => route('system-users.edit', $record->uuid),
                        'class' => 'btnEditStaff',
                        'btnType' => 'edit',
                    ])->render();
                }

                if (Gate::check('staff_delete')) {
                    // delete
                    $buttons .= view('Components::action-button', [
                        'route' => route('system-users.destroy', $record->uuid),
                        'class' => 'deleteStaffBtn',
                        'btnType' => 'delete',
                    ])->render();
                }
                $buttons .= '</div>';
                return $buttons;
            })
            ->setRowId('id')

            ->filterColumn('created_at', function ($query, $keyword) {
                $searchDateFormat = config('constant.search_date_format.date_time');
                $query->whereRaw("DATE_FORMAT(users.created_at,'$searchDateFormat') like ?", ["%$keyword%"]); //date_format when searching using date
            })
            ->filterColumn('role_name', function($query, $keyword) {
                $query->where('roles.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('status', function ($query, $keyword) {
                $statusSearch  = null;
                if (Str::contains('active', strtolower($keyword))) {
                        $statusSearch = 'active';
                } else if (Str::contains('inactive', strtolower($keyword))) {
                        $statusSearch = 'inactive';
                }
                $query->where('status', $statusSearch); 
            })
            ->rawColumns(['action', 'status', 'name']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {         
         return $model->newQuery()
        ->select('users.*', 'roles.name as role_name')
        ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
        ->whereNotIn('roles.id', config('constant.roles'));
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $orderByColumn = 6; // Default order by created_at   
        return $this->builder()
                    ->setTableId('staff-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy($orderByColumn, 'desc') // default order by created_at desc              
                    ->selectStyleSingle()
                    // ->lengthMenu($pagination['lengthMenu'])
                    ->pageLength(50)
                    ->parameters([
                        // 'pageLength' => $pagination['pageLength'],
                        //'dom' => 'frtip',
                        'responsive' => true, // keep responsive enabled
                        'pagingType' => 'simple_numbers',
                        'language' => [
                            'emptyTable' => 'No records available',
                        ],
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [];

        $columns[] = Column::make('DT_RowIndex')->title(trans('global.sno'))->orderable(false)->searchable(false)->addClass('dt-sno');
      
        $columns[] = Column::make('name')->title(trans('cruds.staff.fields.name'))->addClass('w-120px');
        $columns[] = Column::make('role_name')->title(trans('cruds.staff.fields.role'))->addClass('w-100px');
        $columns[] = Column::make('phone')->title(trans('cruds.staff.fields.phone'))->addClass('w-100px');
        $columns[] = Column::make('email')->title(trans('cruds.staff.fields.email'))->addClass('w-120px')->visible(false);
        $columns[] = Column::make('status')->title(trans('cruds.staff.fields.status'))->addClass('w-100px');
        $columns[] = Column::make('created_at')->title(trans('cruds.staff.fields.created_at'))->addClass('dt-created_at')->addClass('w-100px');
       
        $columns[] = Column::computed('action')->orderable(false)->exportable(false)->printable(false)->width(300)->addClass('text-center w-150px');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'staffs_' . date('YmdHis');
    }
}
