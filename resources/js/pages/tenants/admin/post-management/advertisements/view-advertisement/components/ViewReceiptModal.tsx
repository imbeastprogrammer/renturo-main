import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
} from '@/components/ui/dialog';
import { CloseIcon } from '@/assets/tenant/list-of-properties';

type ViewReceiptModal = {
    isOpen: boolean;
    id: number;
    onClose: () => void;
};

function ViewReceiptModal({ isOpen, onClose }: ViewReceiptModal) {
    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className='max-w-[400px] gap-4'>
                <DialogHeader>
                    <button onClick={onClose} className='ml-auto inline-block'>
                        <img src={CloseIcon} alt='modal close icon' />
                    </button>
                </DialogHeader>
                <div className='mx-auto h-[450px] w-[300px] bg-metalic-blue'></div>
                <div className='mx-auto w-[300px] space-y-2'>
                    <div className='flex gap-2 text-sm'>
                        <span className='font-medium text-black/50'>
                            Invoice No
                        </span>
                        <span>000-000-000</span>
                    </div>
                    <div className='flex gap-2 text-sm'>
                        <span className='font-medium text-black/50'>
                            Date received
                        </span>
                        <span>March 08, 2023</span>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}

export default ViewReceiptModal;
