import { useEffect, useState } from 'react';

const useCountdown = (
    initialCountdown: number,
    interval = 1000,
    onComplete?: () => void,
) => {
    const storedTime = parseInt(localStorage.getItem('countdown') || '0');
    const [timeRemaining, setTimeRemaining] = useState(
        storedTime || initialCountdown,
    );

    useEffect(() => {
        let timer: number;
        if (timeRemaining > 0) {
            timer = setInterval(() => {
                const time = timeRemaining - 1;
                setTimeRemaining(time);
                localStorage.setItem('countdown', time.toString());
            }, interval);
        } else {
            if (onComplete) {
                onComplete();
            }
        }

        return () => {
            clearInterval(timer);
        };
    }, [timeRemaining, interval, onComplete]);

    const reset = (newCountdown: number) => {
        localStorage.setItem('countdown', newCountdown.toString());
        setTimeRemaining(newCountdown);
    };

    return {
        timeRemaining,
        reset,
    };
};

export default useCountdown;
