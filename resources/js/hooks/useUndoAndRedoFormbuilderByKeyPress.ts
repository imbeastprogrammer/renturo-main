import { useCallback, useEffect } from 'react';

type Params = { undo: () => void; redo: () => void };

const useUndoAndRedoFormbuilderByKeyPress = ({ undo, redo }: Params) => {
    const handleKeyPress = useCallback((event: KeyboardEvent) => {
        if (event.ctrlKey && event.key === 'z') undo();
        if (event.ctrlKey && event.key === 'r') redo();
    }, []);

    useEffect(() => {
        document.addEventListener('keydown', handleKeyPress);

        return () => {
            document.removeEventListener('keydown', handleKeyPress);
        };
    }, [handleKeyPress]);
};

export default useUndoAndRedoFormbuilderByKeyPress;
