<div class="relative w-full">
    <form wire:submit="search">
        <x-input icon="o-magnifying-glass" placeholder="Search donors, donations, references..." wire:model="query"
            class="input-sm w-full bg-base-200 border-transparent focus:bg-base-100 focus:border-primary transition-all" />
    </form>
</div>
