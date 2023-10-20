import { Accordion } from '@/components/ui/accordion';
import { FormFields } from '../..';
import PropertyEditor from './PropertyEditor';

type PropertiesProps = {
    items: FormFields[];
};
function Properties({ items }: PropertiesProps) {
    return (
        <div className='bg-[#f4f4f4] p-4 py-8'>
            <Accordion type='single' className='space-y-4' collapsible>
                {items.map((item, i) => (
                    <PropertyEditor key={item.id} index={i} item={item} />
                ))}
            </Accordion>
        </div>
    );
}

export default Properties;
