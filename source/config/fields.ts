import {TFieldsMapping} from "@app-types/TFieldsMapping";
import {TFieldType} from "@app-types/TFieldType";
import Input from "../ui/Input";
import Password from "../ui/Password";
import Select from "../ui/Select";
import Textarea from "../ui/Textarea";
import PhoneInput from "../ui/PhoneInput/PhoneInput";
import Checkbox from "../ui/Checkbox";
import RadioGroup from "../ui/RadioGroup";
import Datepicker from "../ui/Datepicker/Datepicker";
import NumberInput from "../ui/Input/NumberInput";
import MultiSelect from "../ui/MultiSelect";
import NumericInput from "../ui/Input/NumericInput";

const getField = ({mapping, type}: { mapping: TFieldsMapping, type: TFieldType }) => {
	return mapping[type];
};

const fieldsMapping: TFieldsMapping = Object.freeze({
	"Input": Input,
	"InputNumber": NumberInput,
	"InputNumeric": NumericInput,
	"Password": Password,
	"Select": Select,
	"MultiSelect": MultiSelect,
	"Textarea": Textarea,
	"Phone": PhoneInput,
	"Checkbox": Checkbox,
	"Radio": RadioGroup,
	"Date": Datepicker,
	"Array": null
});

export { fieldsMapping, getField };
