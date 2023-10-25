import { z } from 'zod';
import { TrashIcon } from 'lucide-react';
import { FormElement, FormElementInstance } from '../FormElement';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import useFormBuilder from '@/hooks/useFormBuilder';

const extraAttributes = {
    is_required: false,
    label: 'Editable Label',
    allow_only_specific_file_types: false,
    maximum_number_of_files: '1',
    maximum_file_size: '10mb',
};

const schema = z.object({
    is_required: z.boolean(),
    label: z.string(),
    allow_only_specific_file_types: z.boolean(),
    maximum_number_of_files: z.string(),
    maximum_file_size: z.string(),
});

const AttachmentField: FormElement = {
    type: 'attachment',
    construct: (id: string) => ({
        id,
        type: 'attachment',
        extraAttributes,
    }),
    designerComponent: DesignerComponent,
    propertiesComponent: PropertiesComponent,
};

type DesignerComponentProps = {
    element: FormElementInstance;
};
function DesignerComponent({ element }: DesignerComponentProps) {
    const { removeField, setSelectedField } = useFormBuilder();
    const elementInstance = element as FormElementInstance & {
        extraAttributes: typeof extraAttributes;
    };

    return (
        <div
            className='w-full rounded-lg border bg-white p-4 shadow-lg'
            onSelect={() => setSelectedField(element)}
        >
            <div className='flex justify-between'>
                <h1>{element.type}</h1>
                <TrashIcon onClick={() => removeField(element.id)} />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2'>
                <Label className='text-[20px]'>
                    {elementInstance.extraAttributes.label}
                </Label>
                <Input type='file' />
            </div>
        </div>
    );
}

type PropertiesComponentProps = {
    element: FormElementInstance;
};
function PropertiesComponent({ element }: PropertiesComponentProps) {
    const { updateField } = useFormBuilder();
    const form = useForm<z.infer<typeof schema>>({
        defaultValues: element.extraAttributes,
        resolver: zodResolver(schema),
    });

    const applyChanges = form.handleSubmit((values) => {
        updateField(element.id, {
            ...element,
            extraAttributes: { ...values },
        });
    });

    return (
        <AccordionItem value={element.id} className='border-0'>
            <AccordionTrigger className='mb-2 rounded-lg bg-white p-3 px-4'>
                {element.type}
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
                        <div className='space-y-2 rounded-lg bg-white px-4 py-2'>
                            <h1 className='mb-4'>Settings</h1>
                            <FormField
                                name='allow_only_specific_file_types'
                                control={form.control}
                                render={({ field }) => (
                                    <FormItem className='flex items-center justify-between space-y-0'>
                                        <FormLabel className='text-[12px]'>
                                            Allow only specific file types
                                        </FormLabel>
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
                                name='maximum_number_of_files'
                                control={form.control}
                                render={({ field }) => (
                                    <FormItem className='flex items-center justify-between gap-4 space-y-0'>
                                        <FormLabel className='flex-shrink-0 text-[12px]'>
                                            Maximum number of files
                                        </FormLabel>
                                        <FormControl>
                                            <Select
                                                value={field.value}
                                                onValueChange={field.onChange}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value='1'>
                                                        1
                                                    </SelectItem>
                                                    <SelectItem value='2'>
                                                        2
                                                    </SelectItem>
                                                    <SelectItem value='3'>
                                                        3
                                                    </SelectItem>
                                                    <SelectItem value='4'>
                                                        4
                                                    </SelectItem>
                                                    <SelectItem value='5'>
                                                        5
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </FormControl>
                                    </FormItem>
                                )}
                            />
                            <FormField
                                name='maximum_file_size'
                                control={form.control}
                                render={({ field }) => (
                                    <FormItem className='flex items-center justify-between gap-4 space-y-0'>
                                        <FormLabel className='flex-shrink-0 text-[12px]'>
                                            Maximum filesize
                                        </FormLabel>
                                        <FormControl>
                                            <Select
                                                value={field.value}
                                                onValueChange={field.onChange}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value='5mb'>
                                                        5mb
                                                    </SelectItem>
                                                    <SelectItem value='10mb'>
                                                        10mb
                                                    </SelectItem>
                                                    <SelectItem value='15mb'>
                                                        15mb
                                                    </SelectItem>
                                                    <SelectItem value='20mb'>
                                                        20mb
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </FormControl>
                                    </FormItem>
                                )}
                            />
                        </div>
                    </form>
                </Form>
            </AccordionContent>
        </AccordionItem>
    );
}

export default AttachmentField;
