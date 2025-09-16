<!DOCTYPE html>
<html>
<head>
    @if($completed)
    <title> Assignment complete Notice</title>
@else
<title> Assignment Incomplete Notice</title>
@endif
</head>
<body>
    <h1>Dear {{$assignerMail}}.</h1>
    @if($completed)
    <h2>{{$userMail}} have completed todo titled "{{$todoTitle}}". </h2>
@else
<h2>{{$userMail}} have marked todo titled "{{$todoTitle}}" as Incomplete. </h2>
@endif
 
</body>
</html>