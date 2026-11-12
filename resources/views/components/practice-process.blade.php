@props(['steps'])

<div class="practice-process-scroll" tabindex="0" role="region" aria-label="Seguimiento de las etapas de práctica">
    <div class="practice-process" role="list">
        @foreach (collect($steps)->chunk(3) as $group)
            <div class="practice-process-group" role="presentation">
                @foreach ($group as $step)
                    <div class="practice-process-step {{ $step['class'] ?? '' }}" role="listitem" data-stage="{{ $step['name'] }}">
                        <div class="practice-process-icon" aria-hidden="true"><i class="bi {{ $step['icon'] }}"></i></div>
                        <strong class="practice-process-name">
                            @if (!empty($step['href']))<a href="{{ $step['href'] }}">{{ $step['name'] }}</a>@else{{ $step['name'] }}@endif
                        </strong>
                        <span class="practice-process-state">{{ $step['state'] }}</span>
                        @if (!empty($step['description']))<small class="practice-process-detail">{{ $step['description'] }}</small>@endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
