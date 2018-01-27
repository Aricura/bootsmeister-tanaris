<span class="kills">{{ $kills }}</span>
<span class="difficulty">{{ $mode }}</span>
<span class="icon">
	@if ($kills >= $bosses)
		<i class="glyphicon glyphicon-ok"></i>
	@elseif ($kills > 0)
		<i class="glyphicon glyphicon-hourglass"></i>
	@else
		<i class="glyphicon glyphicon-remove"></i>
	@endif
</span>