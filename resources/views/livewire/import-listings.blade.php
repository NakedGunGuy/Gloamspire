<div>
    <flux:modal.trigger name="import-cards">
        <flux:button size="sm" icon-trailing="arrow-up-tray" variant="primary">Import</flux:button>
    </flux:modal.trigger>

    <flux:modal name="import-cards" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Import cards from xls</flux:heading>
            <flux:subheading>Make sure your file contains these headers in the <b>FIRST ROW:</b></flux:subheading>
            <flux:subheading class="mt-2">[Set Prefix, Collector Number, Amount, Price]</flux:subheading>
        </div>

        <flux:input type="file" wire:model="file" />

        <div class="flex">
            <flux:spacer />

            <flux:button wire:click="import" variant="primary">Import</flux:button>
        </div>
    </flux:modal>
</div>
