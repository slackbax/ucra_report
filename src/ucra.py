import sys
import pandas as pd
from matplotlib import pyplot as plt
import numpy as np
import chardet


def detect_file_format(file_path):
    with open(file_path, 'rb') as f:
        raw_data = f.read(10000)
        encoding = chardet.detect(raw_data)['encoding']
    df = pd.read_csv(file_path, encoding=encoding)
    return df, encoding


def map_columns(df):
    date_col = "Date"
    systolic_col = "Systolic"
    diastolic_col = "Diastolic"
    pulse_col = "Pulse"
    return date_col, systolic_col, diastolic_col, pulse_col

folder = sys.argv[1]
file_path = '../upload/' + folder + '/data.csv'

df, encoding = detect_file_format(file_path)
date_col, systolic_col, diastolic_col, pulse_col = map_columns(df)

# Pre-process the DataFrame
df = df.iloc[::-1]  # Invert rows
df.reset_index(drop=True, inplace=True)  # Reset index
df["eje_x"] = df[date_col].astype(str)

# Calculate limits for the plot
y_max = df[systolic_col].max()
y_min = df[diastolic_col].min()
pa_max = df[pulse_col].max()
pa_min = df[pulse_col].min()

lista_de_dias = df[date_col].unique().tolist()[:-1]
mediciones_x_dia = [len(df[df[date_col] == dia]) for dia in lista_de_dias]
mediciones_x_dia_std = list(np.cumsum(mediciones_x_dia))

# Plot Blood Pressure
plt.figure(figsize=(16, 6))
plt.title("Evolución de Presión Arterial", weight='bold', fontsize='xx-large')
plt.plot(df[systolic_col], linestyle='solid', color='#dc3545', label='Sistólica')
plt.plot(df[diastolic_col], linestyle='solid', color='#325285', label='Diastólica')
plt.hlines([140, 90], -1, len(df), linestyle='dashed', colors=['#dc3545', '#325285'])
plt.fill_between(np.arange(0, len(df)), df[diastolic_col], df[systolic_col], color='#b7b7b7', alpha=0.5)
plt.xticks(np.arange(0, len(df)), df["eje_x"], rotation=45, ha='right')
plt.xlabel("Momento de medición", weight='bold')
plt.ylabel("[mmHg]", weight='bold')
plt.tight_layout()
plt.grid()
plt.legend()
plt.savefig('../upload/' + folder + '/BP_plot.png', format='png', dpi=300)

# Plot Heart Rate
plt.figure(figsize=(16, 6))
plt.title("Evolución de Frecuencia Cardiaca", weight='bold', fontsize='xx-large')
plt.plot(df[pulse_col], linestyle='solid', color='#5AA19C', label='Latidos por minuto')
plt.xticks(np.arange(0, len(df)), df["eje_x"], rotation=45, ha='right')
plt.xlabel("Momento de medición")
plt.ylabel("[BPM]")
plt.tight_layout()
plt.grid()
plt.legend()
plt.savefig('../upload/' + folder + '/HR_plot.png', format='png', dpi=300)
