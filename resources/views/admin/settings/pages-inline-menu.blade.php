<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('admin/business-settings/terms-condition') ?'active':'' }}"><a href="{{route('admin.business-settings.terms-condition')}}">{{\App\CPU\translate('Terms_&_Conditions')}}</a></li>
        <li class="{{ Request::is('admin/business-settings/privacy-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.privacy-policy')}}">{{\App\CPU\translate('Privacy_Policy')}}</a></li>
        <li class="{{ Request::is('admin/business-settings/page/refund-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['refund-policy'])}}">{{\App\CPU\translate('Refund_Policy')}}</a></li>
        <li class="{{ Request::is('admin/business-settings/page/return-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['return-policy'])}}">{{\App\CPU\translate('Return_Policy')}}</a></li>
        <li class="{{ Request::is('admin/business-settings/page/cancellation-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['cancellation-policy'])}}">{{\App\CPU\translate('Cancellation_Policy')}}</a></li>
        <li class="{{ Request::is('admin/business-settings/page/shipping-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['shipping-policy'])}}">{{\App\CPU\translate('Shipping Policy')}}</a></li>
        <!-- <li class="{{ Request::is('admin/business-settings/page/payment-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['payment-policy'])}}">{{\App\CPU\translate('Payment Policy')}}</a></li> -->
        <!-- <li class="{{ Request::is('admin/business-settings/page/security-policy-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['security-policy-policy'])}}">{{\App\CPU\translate('Account Security Policy')}}</a></li> -->


        <!-- <li class="{{ Request::is('admin/business-settings/page/condition-of-use-policy') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['condition-of-use-policy'])}}">{{\App\CPU\translate('Condition of Use & Sales')}}</a></li> -->
        <!-- <li class="{{ Request::is('admin/business-settings/page/security-information') ?'active':'' }}"><a href="{{route('admin.business-settings.page',['security-information'])}}">{{\App\CPU\translate('Security Information')}}</a></li> -->
        
        
        <li class="{{ Request::is('admin/business-settings/about-us') ?'active':'' }}"><a href="{{route('admin.business-settings.about-us')}}">{{\App\CPU\translate('About_Us')}}</a></li>
        <li class="{{ Request::is('admin/helpTopic/list') ?'active':'' }}"><a href="{{route('admin.helpTopic.list')}}">{{\App\CPU\translate('FAQ')}}</a></li>
        @if(theme_root_path() == 'theme_fashion')
        <!-- <li class="{{ Request::is('admin/business-settings/features-section') ?'active':'' }}"><a href="{{route('admin.business-settings.features-section')}}">{{translate('features_Section')}}</a></li> -->
        @endif
    </ul>
</div>
