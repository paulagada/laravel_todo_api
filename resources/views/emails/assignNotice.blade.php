<!DOCTYPE html>
<html>
<head>
    @if($assigned)
    <title> Assignment Notice</title>
@else
<title> Unassignment Notice</title>
@endif
</head>
<body>
    <h1>Dear {{$userMail}}.</h1>
    @if($assigned)
    <h2>You have been assigned a todo titled "{{$todoTitle}}" by {{$assignerMail}}</h2>
@else
<h2>You have been unassigned a todo titled "{{$todoTitle}}"  by {{$assignerMail}}</h2>
@endif
 
</body>
</html>