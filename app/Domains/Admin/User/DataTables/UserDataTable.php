<?php

namespace App\Domains\Admin\User\DataTables;

use App\Domains\Core\User\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    public $customPageLength = 10;

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable(
            $query->select('users.*')->with('roles')
        ))
            ->addIndexColumn()

            ->editColumn(
                'created_at',
                fn($record) =>
                $record->created_at
                    ? $record->created_at->format(config('constant.date_format.date_time'))
                    : ''
            )

            ->editColumn(
                'name',
                fn($record) =>
                $record->name ? ucwords($record->name) : ''
            )

            ->editColumn('status', function ($record) {
                $checked = $record->status === 'active' ? 'checked' : '';

                if ($record->approval_status == 1) {
                    // Approved → show toggle switch
                    return '
                    <div class="checkbox switch">
                        <label>
                            <input type="checkbox"
                                class="switch-control user_status_cb"
                                ' . $checked . '
                                data-user_id="' . $record->uuid . '" />
                            <span class="switch-label"
                                data-active="' . trans('global.active') . '"
                                data-inactive="' . trans('global.inactive') . '"></span>
                        </label>
                    </div>';
                }

                // Handle rejected
                $statusText = '';
                if ($record->approval_status == 2) {
                    $statusText = '<span class="badge bg-danger">Rejected</span>';
                } elseif ($record->approval_status == 1) {
                    $statusText = '<span class="badge bg-success">Approved</span>';
                }

                $buttonsHtml = '';
                // Show approve/reject buttons only if pending
                if ($record->approval_status === 0) {
                    $buttonsHtml = '
                    <div class="d-flex gap-1">
                        <button type="button"
                                class="btn btn-sm btn-success user_approval_status user_approval_status"
                                data-status="1"
                                data-user_id="' . $record->uuid . '"
                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.(trans('global.approve')).'">
                            <i class="ri-check-line"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger user_approval_status user_approval_status"
                                data-status="2"
                                data-user_id="' . $record->uuid . '"
                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.(trans('global.reject')).'">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>';
                }

                return $buttonsHtml . '<div>' . $statusText . '</div>';
            })


            ->addColumn('action', function ($record) {
                $buttons = '<div class="action-col">';

                if ($record->approval_status == 1) {
                    if (Gate::check('user_change_password')) {
                        $buttons .= view('Components::action-button', [
                            'route' => route('users.change-password', $record->uuid),
                            'class' => 'btnChangePassword',
                            'btnType' => 'change_password',
                        ])->render();
                    }

                    if (Gate::check('user_edit')) {
                        $buttons .= view('Components::action-button', [
                            'route' => route('users.edit', $record->uuid),
                            'class' => 'btnEditUser',
                            'btnType' => 'edit',
                        ])->render();
                    }

                    if (Gate::check('user_delete')) {
                        $buttons .= view('Components::action-button', [
                            'route' => route('users.destroy', $record->uuid),
                            'class' => 'deleteUserBtn',
                            'btnType' => 'delete',
                        ])->render();
                    }
                }

                if (Gate::check('user_show')) {
                    $buttons .= view('Components::action-button', [
                        'route' => route('users.show', $record->uuid),
                        'class' => 'btnViewUser',
                        'btnType' => 'view',
                    ])->render();
                }
                $buttons .= '</div>';
                return $buttons;
            })

            ->setRowId('id')

            ->filterColumn('created_at', function ($query, $keyword) {
                $searchDateFormat = config('constant.search_date_format.date_time');
                $query->whereRaw("DATE_FORMAT(users.created_at,'$searchDateFormat') like ?", ["%$keyword%"]);
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (Str::contains($keyword, 'active')) {

                    $query->where('status', 'active');

                    
                } elseif (Str::contains($keyword, 'inactive')) {
                    $query->where('status', 'inactive');
                } elseif (Str::contains($keyword, 'pending')) {
                    $query->where('approval_status', 0);
                } elseif (Str::contains($keyword, 'approved')) {
                    $query->where('approval_status', 1);
                } elseif (Str::contains($keyword, 'rejected') || Str::contains($keyword, 'declined')) {
                    $query->where('approval_status', 2);
                }
            })

            ->filterColumn('approval_status', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $search = null;
                if (Str::contains($keyword, 'approved')) {
                    $search = 1;
                } elseif (Str::contains($keyword, 'rejected') || Str::contains($keyword, 'declined')) {
                    $search = 2;
                } elseif (Str::contains($keyword, 'pending')) {
                    $search = 0;
                }
                if ($search !== null) {
                    $query->where('approval_status', $search);
                }
            })
            ->rawColumns(['action', 'status']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->whereHas('roles', function ($q) {
            $q->where('id', config('constant.roles.customer'));
        });
    }


    public function html(): HtmlBuilder
    {
        $orderByColumn = 5; // Default order by created_at   
        return $this->builder()
                    ->setTableId('user-table')
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

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(trans('global.sno'))->orderable(false)->searchable(false)->addClass('dt-sno'),

            Column::make('name')->title(trans('cruds.user.fields.name')),
            Column::make('phone')->title(trans('cruds.user.fields.phone')),
            Column::make('email')->title(trans('cruds.user.fields.email')),
            Column::make('status')->title(trans('cruds.user.fields.status')),
            Column::make('created_at')->title(trans('cruds.user.fields.created_at'))->addClass('dt-created_at'),

            Column::computed('action')->orderable(false)->exportable(false)->printable(false)->width(300)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'host_users_' . date('YmdHis');
    }
}
