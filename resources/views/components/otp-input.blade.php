{{-- iOS 26 Style OTP Input --}}
@props(['digits' => 6, 'model' => ''])

<div x-data="{
    otp: Array({{ $digits }}).fill(''),
    value: @entangle($model),
    focusNext(e, index) {
        const inputs = e.target.parentElement.querySelectorAll('input');
        if (inputs[index + 1]) inputs[index + 1].focus();
    },
    focusPrev(e, index) {
        const inputs = e.target.parentElement.querySelectorAll('input');
        if (inputs[index - 1]) inputs[index - 1].focus();
    },
    submitForm(e) {
        const form = e.target.closest('form');
        if (form) setTimeout(() => form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true })), 150);
    },
    isComplete() {
        return this.otp.every(digit => digit !== '');
    },
    handleInput(e, index) {
        const val = e.target.value.replace(/\D/g, '');
        const digit = val ? val[val.length - 1] : '';
        
        this.otp[index] = digit;
        e.target.value = digit;

        const inputs = e.target.parentElement.querySelectorAll('input');

        if (digit) {
            if (index < {{ $digits }} - 1 && inputs[index + 1]) {
                inputs[index + 1].focus();
            } else if (this.isComplete()) {
                this.submitForm(e);
            }
        }

        this.value = this.otp.join('');
    },
    handleBackspace(e, index) {
        const inputs = e.target.parentElement.querySelectorAll('input');
        
        if (!e.target.value && inputs[index - 1]) {
            inputs[index - 1].focus();
        }
    },
    handlePaste(e) {
        const text = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/\D/g, '');

        if (text.length >= {{ $digits }}) {
            const inputs = e.target.parentElement.querySelectorAll('input');

            // Fill all inputs with the first digits available
            for (let i = 0; i < {{ $digits }}; i++) {
                this.otp[i] = text[i];
                inputs[i].value = text[i];
            }

            this.value = this.otp.join('');

            // Focus last input and trigger submit
            this.$nextTick(() => {
                inputs[{{ $digits }} - 1].focus();
                this.submitForm(e);
            });
        }
        e.preventDefault();
    }
}" class="flex gap-2.5 justify-center" dir="ltr">

    @for($i = 0; $i < $digits; $i++)
        <input
            type="tel"
            maxlength="1"
            inputmode="numeric"
            pattern="[0-9]*"
            autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
            @input="handleInput($event, {{ $i }})"
            @keydown.backspace="handleBackspace($event, {{ $i }})"
            @paste.prevent="handlePaste($event)"
            class="w-11 h-14 text-center text-xl font-black rounded-2xl
                   bg-black/5 dark:bg-white/10
                   border-2 border-transparent
                   focus:border-fadebook-accent focus:ring-0 focus:bg-white dark:focus:bg-[#2c2c2e]
                   text-gray-900 dark:text-white
                   transition-all duration-200 outline-none
                   [&:-webkit-autofill]:[transition:background-color_9999999s_ease-in-out_0s]
                   [&:-webkit-autofill]:[-webkit-text-fill-color:inherit]"
        >
    @endfor
</div>
