import { Accordion } from '@/components/ui/accordion';
import { FormElements } from './FormElement';
import useFormBuilder from '@/hooks/useFormBuilder';

function Properties() {
    const {
        pages,
        current_page_id: current_page,
        selectedField,
        setSelectedField,
    } = useFormBuilder();
    const currentPage = pages.find((page) => page.page_id === current_page);

    return (
        <div className='overflow-hidden'>
            <Accordion
                type='single'
                collapsible
                value={selectedField?.id}
                className='h-full overflow-auto p-4 py-8'
            >
                {currentPage?.fields.map((field) => {
                    const PropertyEditor =
                        FormElements[field.type].propertiesComponent;

                    return (
                        <div onClick={() => setSelectedField(field)}>
                            <PropertyEditor key={field.id} element={field} />
                        </div>
                    );
                })}
            </Accordion>
        </div>
    );
}

export default Properties;
