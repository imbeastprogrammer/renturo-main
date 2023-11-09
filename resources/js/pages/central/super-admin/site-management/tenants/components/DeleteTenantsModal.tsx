import _ from 'lodash';
import { DeleteWarning } from '@/assets/central';
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { useToast } from '@/components/ui/use-toast';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

type DeleteModalProps = {
    isOpen: boolean;
    onClose: () => void;
    id: string;
};
function DeleteTenantsModal({ isOpen, onClose, id }: DeleteModalProps) {
    const { toast } = useToast();

    const handleDelete = () => {
        router.delete(`/super-admin/tenants/${id}`, {
            onSuccess: () => {
                onClose();
                toast({
                    title: 'Success',
                    description: 'The new user has been deleted to the system.',
                    variant: 'default',
                });
            },
            onError: (error) => {
                onClose();
                toast({
                    title: 'Error',
                    description:
                        _.valuesIn(error)[0] ||
                        'Something went wrong, Please try again later.',
                    variant: 'destructive',
                });
            },
        });
    };

    return (
        <Dialog open={isOpen} onOpenChange={() => onClose()}>
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
                        This action will remove the tenant and all of associated
                        data from the system.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter className='w-full'>
                    <div className='flex w-full flex-col items-center gap-2'>
                        <Button
                            variant='destructive'
                            className='h-[25px] w-[217px]'
                            onClick={handleDelete}
                        >
                            Delete Tenant
                        </Button>
                        <Button
                            variant='outline'
                            className='h-[25px] w-[217px]'
                        >
                            Cancel
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

export default DeleteTenantsModal;
