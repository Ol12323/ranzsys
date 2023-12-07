<div class="flex items-center px-4 py-3"
    x-data="{
        state: @js($getState())
    }" 
    x-init="
        $watch('state', () => {
           $wire.updateTableColumnState(
            @js($getName()),
            @js($recordKey),
            state,
           )
        })
    "
>
    <span @click="state = state > 1 ? state - 1 : 1" class="text-2xl cursor-pointer">-</span>
    <span class="mx-2" x-text="state"></span>
    <span @click="state = state + 1" class="text-2xl cursor-pointer">+</span>
</div>