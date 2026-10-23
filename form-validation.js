document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-no-spaces]').forEach((input) => {
        input.addEventListener('keydown', (event) => {
            if (event.key === ' ' || event.code === 'Space') {
                event.preventDefault();
            }
        });

        input.addEventListener('beforeinput', (event) => {
            if (event.data && /\s/.test(event.data)) {
                event.preventDefault();
            }
        });

        input.addEventListener('input', () => {
            if (/\s/.test(input.value)) {
                const cursor = input.selectionStart;
                const cursorAfterCleanup = cursor === null
                    ? null
                    : input.value.slice(0, cursor).replace(/\s/g, '').length;
                input.value = input.value.replace(/\s/g, '');
                if (cursor !== null) {
                    input.setSelectionRange(cursorAfterCleanup, cursorAfterCleanup);
                }
            }
        });
    });

    document.querySelectorAll('[data-digits-only]').forEach((input) => {
        input.addEventListener('input', () => {
            const cursor = input.selectionStart;
            const digitsBeforeCursor = cursor === null
                ? null
                : input.value.slice(0, cursor).replace(/\D/g, '').length;
            input.value = input.value.replace(/\D/g, '');
            if (digitsBeforeCursor !== null) {
                input.setSelectionRange(digitsBeforeCursor, digitsBeforeCursor);
            }
        });
    });

    document.querySelectorAll('[data-voyage-phone]').forEach((input) => {
        const validatePhone = () => {
            if (/[- ]/.test(input.value)) {
                const cursor = input.selectionStart;
                const cursorAfterCleanup = cursor === null
                    ? null
                    : input.value.slice(0, cursor).replace(/[- ]/g, '').length;
                input.value = input.value.replace(/[- ]/g, '');
                if (cursor !== null) {
                    input.setSelectionRange(cursorAfterCleanup, cursorAfterCleanup);
                }
            }
            const digitCount = input.value.replace(/\D/g, '').length;
            let validationMessage = '';
            if (input.value && !input.value.startsWith('+')) {
                validationMessage = 'Start your phone number with +.';
            } else if (input.value && (digitCount < 7 || digitCount > 15)) {
                validationMessage = 'Enter 7 to 15 digits after the leading +, with no spaces or punctuation.';
            }
            input.setCustomValidity(validationMessage);
        };

        input.addEventListener('input', validatePhone);
        validatePhone();
    });
});