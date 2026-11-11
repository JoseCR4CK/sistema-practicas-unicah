<div class="jury-grid">
    @forelse ($members as $member)
        <article class="jury-card">
            <div class="jury-avatar">{{ Str::initials($member->user->name, true) }}</div>
            <div><strong>{{ $member->user->name }}</strong><p>{{ $member->function }}</p></div>
        </article>
    @empty
        <div class="teacher-load-empty">La terna está pendiente de asignación.</div>
    @endforelse
</div>
