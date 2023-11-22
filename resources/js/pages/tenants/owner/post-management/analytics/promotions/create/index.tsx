import { ReactNode } from 'react';
import CreatePromotionLayout from './components/CreatePromotionLayout';
import CreatePromotionForm from './components/CreatePromotionForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function CreatePromotion() {
    return (
        <ScrollArea>
            <CreatePromotionForm />
        </ScrollArea>
    );
}

CreatePromotion.layout = (page: ReactNode) => (
    <CreatePromotionLayout>{page}</CreatePromotionLayout>
);

export default CreatePromotion;
