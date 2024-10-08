import sys
import pandas as pd
from matplotlib import pyplot as plt
import numpy as np
from matplotlib.ticker import MaxNLocator
import chardet

# FOLDER - DATE - SYSTOLIC - DIASTOLIC - PULSE
# READ CSV FILE
folder = sys.argv[1]
date_col = sys.argv[2]  # Columna fecha y hora
systolic_col = sys.argv[3]  # Columna sistolica
diastolic_col = sys.argv[4]  # Columna diastolica
pulse_col = sys.argv[5]  # Columna pulso
file = '../upload/' + folder + '/upload.csv'

# Detección de codificacion con chardet
with open(file, 'rb') as f:
    raw_data = f.read(10000)  # Read a sample of the file
    result = chardet.detect(raw_data)  # Detect the encoding
    encoding = result['encoding']  # Get the detected encoding

# READ CSV FILE
df = pd.read_csv(file, encoding=encoding, sep='\t')

# PRE-PROCESSING
df = df.iloc[::-1]  # Invert the order of rows
df.reset_index(drop=True, inplace=True)  # Reset index after inversion

# CREATE A NEW COLUMN FOR X-AXIS (DATE + TIME)
new_column = []
for i in range(len(df)):
    new_column.append(df[date_col][i][-2:] + "-" + df[date_col][i][-5:-3] + "-" + df[date_col][i][-10:-6] + ' ' + df[date_col][i])

df["eje_x"] = new_column

# Y-AXIS LIMITS
y_max = np.max(df[systolic_col])
y_min = np.min(df[diastolic_col])
pa_max = np.max(df[pulse_col])
pa_min = np.min(df[pulse_col])

# LISTA DE DÍAS Y MEDICIONES POR DÍA
lista_de_dias = df[date_col].unique().tolist()[:-1]
mediciones_x_dia = [len(df.query(f"{date_col} == @dia")) for dia in lista_de_dias]

# CALCULAR SUMA ACUMULADA DE MEDICIONES POR DÍA
mediciones_x_dia_std = []
for count, value in enumerate(mediciones_x_dia):
    if count == 0:
        mediciones_x_dia_std.append(value + 0.5)
    else:
        mediciones_x_dia_std.append(value)

mediciones_x_dia_std = list(np.cumsum(mediciones_x_dia_std))
mediciones_x_dia_std = [x - 1 for x in mediciones_x_dia_std]

# BLOOD PRESSURE GRAPH

plt.figure(figsize=(16, 6))
plt.title("Evolución de Presión Arterial", weight='bold', fontsize='xx-large')
plt.plot(df[systolic_col], 'k', linestyle='solid', color='#dc3545')
plt.plot(df[systolic_col], 'ro', label='Sistólica', color='#dc3545')
plt.plot(df[diastolic_col], 'k', linestyle='solid', color='#325285')
plt.plot(df[diastolic_col], 'bo', label='Diastólica', color='#325285')
we_mean = df[systolic_col] * (1 / 3) + df[diastolic_col] * (2 / 3)
plt.plot(we_mean, color='w')
plt.hlines(140, -1, len(df), linestyle='dashed', color='#dc3545')
plt.hlines(90, -1, len(df), linestyle='dashed', color='#325285')
plt.vlines(mediciones_x_dia_std, y_min, y_max, linestyle='solid', color='black')

# Adjust x-axis label spacing
plt.gca().xaxis.set_major_locator(MaxNLocator(integer=True))

# Rotate x-axis labels
plt.xticks(np.arange(0, len(df)), df["eje_x"], rotation=45, ha='right')

plt.fill_between(np.arange(0, len(df)), df[diastolic_col], df[systolic_col], color='#b7b7b7', alpha=0.5)
plt.xlabel("Momento de medición", weight='bold')
plt.ylabel("[mmHg]", weight='bold')
plt.xlim(-1, len(df))

plt.tight_layout()
plt.grid()
plt.legend()

plt.savefig('../upload/' + folder + '/BP_plot.png', format='png', dpi=300)

# HEART RATE GRAPH

plt.figure(figsize=(16, 6))
plt.title("Evolución de Frecuencia Cardiaca", weight='bold', fontsize='xx-large')
plt.plot(df[pulse_col], 'k', linestyle='solid', color='#5AA19C')
plt.plot(df[pulse_col], 'go', label='Latidos por minuto', color='#5AA19C')
plt.vlines(mediciones_x_dia_std, pa_min, pa_max, linestyle='solid', color='black')

# Adjust x-axis label spacing
plt.gca().xaxis.set_major_locator(MaxNLocator(integer=True))

# Rotate x-axis labels
plt.xticks(np.arange(0, len(df)), df["eje_x"], rotation=45, ha='right')

plt.xlabel("Momento de medición")
plt.ylabel("[BPM]")
plt.xlim(-1, len(df))

plt.tight_layout()
plt.grid()
plt.legend()

plt.savefig('../upload/' + folder + '/HR_plot.png', format='png', dpi=300)
