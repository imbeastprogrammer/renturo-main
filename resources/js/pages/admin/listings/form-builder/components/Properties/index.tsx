import { Accordion } from '@/components/ui/accordion';
import useFormBuilder from '@/hooks/useFormBuilder';
import { FormElements } from '../FormElement';

function Properties() {
    const { fields } = useFormBuilder();

    return (
        <div className='space-y-4 bg-[#f4f4f4] p-4 py-8'>
            <Accordion type='single' collapsible>
                {fields.map((field) => {
                    const PropertyEditor =
                        FormElements[field.type].propertiesComponent;

                    return <PropertyEditor key={field.id} element={field} />;
                })}
            </Accordion>
        </div>
    );
}

export default Properties;
