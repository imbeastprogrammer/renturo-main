import { DeleteWarning } from '@/assets/central';
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { useState } from 'react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

import useCentralToast from '@/hooks/useCentralToast';

type DeleteModalProps = {
    isOpen: boolean;
    onClose: () => void;
    id: number;
};
function DeleteSubCategoryModal({ isOpen, onClose, id }: DeleteModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const toast = useCentralToast();

    const handleDelete = () => {
        router.delete(`/admin/categories/${id}`, {
            onBefore: () => setIsLoading(true),
            onFinish: () => setIsLoading(false),
            onSuccess: () => {
                onClose();
                toast.success({
                    title: 'Success',
                    description:
                        'The new category has been deleted to the system.',
                });
            },
            onError: (error) => {
                onClose();
                toast.error({
                    title: 'Error',
                    description:
                        Object.keys(error)[0] ||
                        'Something went wrong, Please try again later.',
                });
            },
        });
    };

    const handleClose = () => !isLoading && onClose();
    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className='max-w-[386px] space-y-2 font-outfit'>
                <img
                    src={DeleteWarning}
                    alt='Delete warning icon'
                    className='mx-auto h-[58px] w-[58px] object-contain'
                />
                <DialogHeader>
                    <DialogTitle className='text-center text-[20px] font-semibold'>
                        Are you sure?
                    </DialogTitle>
                    <DialogDescription className='text-center text-base font-thin text-black/50'>
                        This action will remove the category and all of their
                        associated data from the system.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter className='w-full'>
                    <div className='flex w-full flex-col items-center gap-2'>
                        <Button
                            variant='destructive'
                            className='h-[25px] w-[217px]'
                            disabled={isLoading}
                            onClick={handleDelete}
                        >
                            Delete Category
                        </Button>
                        <Button
                            variant='outline'
                            className='h-[25px] w-[217px]'
                            onClick={handleClose}
                        >
                            Cancel
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

export default DeleteSubCategoryModal;
