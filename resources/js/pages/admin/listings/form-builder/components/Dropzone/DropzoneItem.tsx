import _ from 'lodash';
import { CSS } from '@dnd-kit/utilities';
import { GripVerticalIcon, TrashIcon } from 'lucide-react';
import { useSortable } from '@dnd-kit/sortable';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input';
import { useController } from 'react-hook-form';
import { FieldTypes, toolboxItems } from '../toolboxItems';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { FormFields } from '../..';
import { Textarea } from '@/components/ui/textarea';

type DropzoneItemProps = {
    index: number;
    item: FormFields;
    onRemove: () => void;
};

function DropzoneItem({ item, onRemove, index }: DropzoneItemProps) {
    const { attributes, listeners, setNodeRef, transform, transition } =
        useSortable({ id: item.id });

    const fieldTypes = _.flattenDeep(toolboxItems.map(({ items }) => items));

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
    };

    const fieldTypeField = useController({
        name: `custom_fields.${index}.type`,
    });

    const labelField = useController({
        name: `custom_fields.${index}.label`,
    });

    const SelectedFieldTypeIcon = fieldTypes.find(
        ({ id }) => id === fieldTypeField.field.value,
    )?.icon;

    return (
        <div
            ref={setNodeRef}
            style={style}
            className='grid grid-cols-[40px_1fr] gap-4 rounded-lg bg-white p-4'
        >
            <div>
                <Button
                    variant='ghost'
                    size='icon'
                    {...attributes}
                    {...listeners}
                >
                    <GripVerticalIcon className='text-blue-500' />
                </Button>
            </div>
            <div className='space-y-2'>
                <div className='flex items-center justify-between'>
                    <Select
                        {...fieldTypeField.field}
                        onValueChange={(value) =>
                            fieldTypeField.field.onChange(value)
                        }
                    >
                        <SelectTrigger className='w-[180px] border-none'>
                            <div className='flex items-center gap-2'>
                                {SelectedFieldTypeIcon && (
                                    <SelectedFieldTypeIcon className='text-metalic-blue' />
                                )}
                                <SelectValue />
                            </div>
                        </SelectTrigger>
                        <SelectContent>
                            {fieldTypes.map((fieldType, idx) => (
                                <SelectItem key={idx} value={fieldType.id}>
                                    {fieldType.title}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Button size='icon' variant='ghost' onClick={onRemove}>
                        <TrashIcon className='cursor-pointer text-red-500' />
                    </Button>
                </div>
                <Separator />
                <div>
                    <h1 className='text-[20px] font-medium'>
                        {labelField.field.value}
                    </h1>
                </div>
                <div className='pointer-events-none'>
                    {renderElement(fieldTypeField.field.value)}
                </div>
            </div>
        </div>
    );
}

const renderElement = (fieldType: FieldTypes) => {
    switch (fieldType) {
        case FieldTypes.TEXT_FIELD:
            return <Input />;
        case FieldTypes.TEXT_AREA:
            return <Textarea />;
        case FieldTypes.NUMBER:
            return <Input type='number' />;
        case FieldTypes.EMAIL:
            return <Input type='email' />;
        case FieldTypes.DATE:
            return <Input type='date' />;
        case FieldTypes.TIME:
            return <Input type='time' />;
        case FieldTypes.DROPDOWN:
            return (
                <Select>
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent></SelectContent>
                </Select>
            );
        case FieldTypes.ATTACHMENT:
            return <Input type='file' />;
        case FieldTypes.IMAGE:
            return <Input type='file' />;
    }
};

export default DropzoneItem;
