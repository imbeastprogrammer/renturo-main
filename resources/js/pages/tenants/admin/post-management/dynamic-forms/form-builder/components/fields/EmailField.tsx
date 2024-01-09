import { z } from 'zod';
import {
    ElementsType,
    FormElement,
    FormElementInstance,
    FormElements,
} from '../FormElement';
import { router } from '@inertiajs/react';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import {
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Switch } from '@/components/ui/switch';

import { DeleteIcon } from '@/assets/form-builder';
import useFormBuilder from '@/hooks/useFormBuilder';
import PropertyEditorHandle from '../PropertyEditorHandle';
import useFieldTypes from '../../hooks/useFieldTypes';
import FieldTypeChanger from '../FieldTypeChanger';

const field = {
    is_required: false,
    label: 'What is your Email?',
};

const schema = z.object({ is_required: z.boolean(), label: z.string() });

const EmailField: FormElement = {
    type: 'email',
    construct: (id) => ({
        id,
        type: 'email',
        ...field,
    }),
    designerComponent: DesignerComponent,
    propertiesComponent: PropertiesComponent,
};

type DesignerComponentProps = {
    element: FormElementInstance;
};
function DesignerComponent({ element }: DesignerComponentProps) {
    const { removeField, setSelectedField, updateField, current_page_id } =
        useFormBuilder();
    const elementInstance = element as FormElementInstance;

    const { currentFieldType, fieldTypes } = useFieldTypes(element.type);

    const handleValueChange = (value: ElementsType) => {
        updateField(
            current_page_id,
            element.id,
            FormElements[value].construct(element.id),
        );
    };

    const handleRemoveField = () => {
        if (typeof element.id === 'string')
            return removeField(current_page_id, element.id);

        router.delete(`/admin/form/fields/${element.id}`, {
            onSuccess: () => removeField(current_page_id, element.id),
        });
    };

    return (
        <div className='w-full' onClick={() => setSelectedField(element)}>
            <div className='flex justify-between'>
                <FieldTypeChanger
                    data={fieldTypes}
                    icon={currentFieldType?.icon}
                    value={currentFieldType?.type}
                    onValueChange={handleValueChange}
                />
                <button onClick={handleRemoveField}>
                    <DeleteIcon />
                </button>
            </div>
            <Separator className='my-2' />
            <div className='pointer-events-none space-y-2'>
                <Label className='text-[20px]'>{elementInstance.label}</Label>
                <Input type='email' />
            </div>
        </div>
    );
}

type PropertiesComponentProps = {
    element: FormElementInstance;
};
function PropertiesComponent({ element }: PropertiesComponentProps) {
    const { updateField, current_page_id } = useFormBuilder();
    const form = useForm<z.infer<typeof schema>>({
        defaultValues: element,
        resolver: zodResolver(schema),
    });

    const { currentFieldType } = useFieldTypes(element.type);

    const applyChanges = form.handleSubmit((values) => {
        updateField(current_page_id, element.id, {
            id: element.id,
            type: element.type,
            ...values,
        });
    });

    return (
        <AccordionItem value={element.id.toString()} className='border-0'>
            <AccordionTrigger className='mb-2 rounded-lg bg-white p-3 px-4'>
                {currentFieldType && (
                    <PropertyEditorHandle
                        type={currentFieldType?.title}
                        icon={currentFieldType.icon}
                    />
                )}
            </AccordionTrigger>
            <AccordionContent>
                <Form {...form}>
                    <form
                        onBlur={applyChanges}
                        onSubmit={applyChanges}
                        className='space-y-2'
                    >
                        <FormField
                            name='is_required'
                            control={form.control}
                            render={({ field }) => (
                                <FormItem className='flex items-center justify-between space-y-0 rounded-lg bg-white px-4 py-3'>
                                    <FormLabel>Required</FormLabel>
                                    <FormControl>
                                        <Switch
                                            checked={field.value}
                                            onCheckedChange={field.onChange}
                                        />
                                    </FormControl>
                                </FormItem>
                            )}
                        />
                        <FormField
                            name='label'
                            control={form.control}
                            render={({ field }) => (
                                <FormItem className='rounded-lg bg-white px-4 py-3'>
                                    <FormLabel>Label</FormLabel>
                                    <FormControl>
                                        <Input {...field} />
                                    </FormControl>
                                </FormItem>
                            )}
                        />
                    </form>
                </Form>
            </AccordionContent>
        </AccordionItem>
    );
}

export default EmailField;
