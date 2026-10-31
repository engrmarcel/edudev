@include('errors.layout', ['code' => 429, 'heading' => __('Too many requests'), 'message' => __('You have made a lot of requests in a short time. Please wait a minute and try again.')])
