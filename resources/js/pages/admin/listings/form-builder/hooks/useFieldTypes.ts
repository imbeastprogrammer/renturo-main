import _ from 'lodash';
import { toolboxItems } from '../components/toolboxItems';
import { ElementsType } from '../components/FormElement';

const useFieldTypes = (fieldType: ElementsType) => {
    const fieldTypes = _.flatMapDeep(toolboxItems.map(({ items }) => items));

    const currentFieldType = fieldTypes.find(
        (fieldItem) => fieldItem.type === fieldType,
    );

    return { fieldTypes, currentFieldType };
};

export default useFieldTypes;
