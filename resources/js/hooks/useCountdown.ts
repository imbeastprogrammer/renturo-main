import { useEffect, useState } from 'react';

const useCountdown = (
    initialCountdown: number,
    interval = 1000,
    onComplete?: () => void,
) => {
    const [countdown, setCountdown] = useState(initialCountdown);

    useEffect(() => {
        let timer: number;
        if (countdown > 0) {
            timer = setInterval(() => {
                setCountdown(countdown - 1);
            }, interval);
        } else {
            if (onComplete) {
                onComplete();
            }
        }

        return () => {
            clearInterval(timer);
        };
    }, [countdown, interval, onComplete]);

    const reset = (newCountdown: number) => {
        setCountdown(newCountdown);
    };

    return {
        countdown,
        reset,
    };
};

export default useCountdown;
