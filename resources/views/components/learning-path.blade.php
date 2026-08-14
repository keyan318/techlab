<div class="path">
  @foreach($nodes as $index => $node)
    <x-lesson-node :node="$node" :index="$index" :is-current="$node['type'] === 'current'" />
    @if(!$loop->last)
      <div class="connector">↓</div>
    @endif
  @endforeach
  <div class="path-end">🏆 MASTER</div>
</div>
