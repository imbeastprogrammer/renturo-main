import { useEffect, useState } from 'react';

const useCountdown = (
    initialCountdown: number,
    interval = 1000,
    onComplete?: () => void,
) => {
    const initialTime =
        JSON.parse(localStorage.getItem('countdown') || '{}') ||
        initialCountdown;
    const [timeRemaining, setTimeRemaining] = useState(initialTime);

    useEffect(() => {
        let timer: number;
        if (timeRemaining > 0) {
            timer = setInterval(() => {
                const time = timeRemaining - 1;
                setTimeRemaining(time);
                localStorage.setItem('countdown', JSON.stringify(time));
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
        localStorage.removeItem('countdown');
        setTimeRemaining(newCountdown);
    };

    return {
        timeRemaining,
        reset,
    };
};

export default useCountdown;
