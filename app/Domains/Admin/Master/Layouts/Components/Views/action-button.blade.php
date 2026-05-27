@if($btnType == 'view')
    <a href="{{ !empty($class) ? 'javascript:void(0);' : $route  }}" @if (!empty($class)) data-href="{{ $route }}" @endif  class="btn btn-outline-info btn-sm {{ $class }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('global.view')"> <i class="ri-eye-fill"></i> </a>
@elseif($btnType == 'edit')
    <a href="{{ !empty($class) ? 'javascript:void(0);' : $route  }}"  @if (!empty($class)) data-href="{{ $route }}" @endif class="btn btn-outline-success btn-sm {{ $class }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('global.edit')"> <i class="ri-edit-2-line"></i> </a>

@elseif($btnType == 'delete')
    <a href="javascript:void(0);" data-href="{{ $route }}" class="btn btn-outline-danger btn-sm {{ $class }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('global.delete')"><i class="ri-delete-bin-line"></i></a>

@elseif($btnType == 'change_password')
    <a href="javascript:void(0);" data-href="{{ $route }}" class="btn btn-outline-light btn-sm {{ $class }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('global.change_password')" > <i class="fa fa-key"></i></a>
    
@elseif($btnType == 'sms')
    <button type="button" data-url="{{ $route }}" data-number="{{ $dataNumber }}" data-name="{{ $dataName }}" data-amount="{{ $dataAmount }}"  data-key="{{ $dataAmount > 0 ? 'pending_amount_sms' : 'hello_sms' }}"  class="btn btn-outline-warning btn-sm {{ $class }} send-sms-btn" data-bs-toggle="tooltip"        data-bs-placement="top" data-bs-title="SMS">
        <i class="ri-message-2-line"></i>
    </button>

@elseif($btnType == 'whatsapp')
    <a href="{{ $route }}" target="_blank"  class="btn btn-outline-success btn-sm {{ $class }}" data-bs-toggle="tooltip" data-bs-placement="top"
       data-bs-title="WhatsApp" data-url="{{ $dataUrl ?? '' }}">
        <i class="ri-whatsapp-line"></i>
    </a>    
@elseif($btnType == 'view-ledger')
    <a href="{{ $route }}" class="btn btn-bg-blue text-white no-hover-bg {{ $class }}" data-bs-toggle="tooltip" data-bs-placement="top"
       data-bs-title="View Ledger">
        <i class="ri-file-list-3-line"></i>
    </a>
@endif